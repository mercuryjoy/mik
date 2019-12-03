<?php

namespace App\Common;

use App\Activity;
use App\NetApiLog;
use App\ScanLog;
use App\ScanWarning;
use App\ShopActivity;
use App\User;
use Carbon\Carbon;

class Common
{
    public static function getShopActivity($shop_id)
    {
        $data = [
            'code' => 400,
            'message' => '没有可用的活动规则',
            'data' => [],
        ];

        if ($shop_id) {
            // QUERY THE ALL SUIT ACTIVITY RULE
            $overall_activities = Activity::where('action_zone', 'all')
                ->get(['title', 'action_zone', 'type', 'rule_json', 'status']);

            $activities = [];
            if ($overall_activities->count() == 3) {
                foreach ($overall_activities as $overall_activity) {
                    $type = $overall_activity->type;
                    $rule_json = $overall_activity->rule_json;

                    if ($type == 'point') {
                        if (!isset($activities['point'])) {
                            $activities['point'] = get_rule_json($rule_json);
                        }
                    } elseif ($type == 'shop_owner') {
                        if (!isset($activities['money'])) {
                            $activities['money'] = get_rule_json($rule_json);
                        }
                    } elseif ($type == 'red_envelope') {
                        $activities['red_envelope'] = $rule_json;
                    }
                }
            }

            // QUERY THE SUIT CONDITIONS'S PART ACTIVITY RULE
            $now_time = date('Y-m-d H:i:s');
            $shop_activities = ShopActivity::with(['activity' => function ($shop_activities) use ($now_time) {
                $shop_activities->where('status', 'normal')
                    ->where('action_zone', 'part')
                    ->where('start_at', '<=', $now_time)
                    ->where('end_at', '>=', $now_time);
            }])
                ->where('shop_id', $shop_id)
                ->get();

            if ($shop_activities->count()) {
                foreach ($shop_activities as $shop_activity) {
                    if ($shop_activity->activity && $shop_activity->activity->id) {
                        $rule_json = $shop_activity->activity->rule_json;
                        $type = $shop_activity->activity->type;

                        if ($type == 'point') {
                            $activities['point'] = get_rule_json($rule_json);
                        } elseif ($type == 'shop_owner') {
                            $activities['money'] = get_rule_json($rule_json);
                        } elseif ($type == 'red_envelope') {
                            $activities['red_envelope'] = $shop_activity->activity->rule_json;
                        }
                    }
                }
            }

            return [
                'code' => 200,
                'message' => '有可用的活动规则',
                'data' => $activities,
            ];

        } else {
            $data['code'] = 400;
            $data['message'] = '您没有选择终端';
        }

        return $data;
    }

    /**
     * @brief 核销优惠券NET接口
     * @author: WT
     * @date: 2018-02-02 17:54
     * @param
     * @return array
     */
    public static function verifyCoupon($code, $user_id, $user_name, $shop_id, $shop_name)
    {
        $paramArr = [
            'Identification' => $code,
            'WaiterId' => $user_id,
            'WaiterName' => $user_name,
            'ShopId' => $shop_id,
            'ShopName' => $shop_name,
        ];
        $param = json_encode($paramArr);
        $url = config('custom.net_url').config('custom.checkout_coupon_api_url').'?data='.$param;
        $data = get_api_data($url, 'get', 'xml');

        $result_data = [
            'code' => 400,
            'message' => '核销失败',
        ];
        if ($data['Msg'] === 'ok') {  // 核销成功

            $coupon_name = $data['Info']['CouponName'];
            $net_user_id = $data['Info']['UserId'];
            $net_user_name = $data['Info']['NickName'];

            $result = ScanLog::create([
                'coupon_code' => $code,
                'coupon_name' => $coupon_name,
                'net_user_id' => $net_user_id,
                'net_user_name' => $net_user_name,
                'shop_id' => $shop_id,
                'user_id' => $user_id,
                'type' => 'scan_coupon',
            ]);

            self::addScanTimeWarning($user_id, $net_user_id, $net_user_name, $shop_id);

            if ($result) {
                $result_data['code'] = 200;
                $result_data['message'] = '核销成功';
                return $result_data;
            } else {
                return $result_data;
            }
        }

        $result_data['message'] = $data['Info'];
        return $result_data;
    }

    /**
     * 添加核销预警信息
     * 查询此用户是否满足每天扫码超过3次/7天扫码超过5次/30天扫码超过/连续两天
     * @param  int $user_id 用户ID
     * @param  int $net_user_id net用户ID
     * @param  int $shop_id 终端ID
     */
    private static function addScanTimeWarning($user_id, $net_user_id, $net_user_name, $shop_id)
    {
        if ($user_id && $net_user_id && $shop_id)
        {
            $date_conditions = [
                'today' => [
                    'start_date' => Carbon::today()->toDateTimeString(),
                    'end_date' => Carbon::now()->toDateTimeString(),
                    'warning_type' => 1,
                    'times' => 3,
                ],
                'seven' => [
                    'start_date' => Carbon::parse('-6 days')->startOfDay()->toDateTimeString(),
                    'end_date' => Carbon::now()->toDateTimeString(),
                    'warning_type' => 2,
                    'times' => 3,
                ],
                'thirty' => [
                    'start_date' => Carbon::parse('-29 days')->startOfDay()->toDateTimeString(),
                    'end_date' => Carbon::now()->toDateTimeString(),
                    'warning_type' => 3,
                    'times' => 5,
                ],
                'keep' => [
                    'start_date' => Carbon::yesterday()->toDateTimeString(),
                    'end_date' => Carbon::yesterday()->endOfDay()->toDateTimeString(),
                    'warning_type' => 4,
                    'times' => 1,
                ]
            ];

            foreach ($date_conditions as $key=>$date_condition) {
                $start_date = $date_condition['start_date'];
                $end_date = $date_condition['end_date'];
                $warning_type = $date_condition['warning_type'];
                $times = $date_condition['times'];


                $scan_times = ScanLog::where('net_user_id', $net_user_id)
                    ->where('shop_id', $shop_id)
                    ->where('type', 'scan_coupon')
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->count();

                if ($scan_times >= $times) {

                    if ($key == 'keep') {
                        // calculate today scan time
                        $scan_times += ScanLog::where('net_user_id', $net_user_id)
                            ->where('shop_id', $shop_id)
                            ->where('type', 'scan_coupon')
                            ->whereBetween('created_at', [Carbon::today()->toDateTimeString(), Carbon::now()])
                            ->count();
                    }

                    ScanWarning::create([
                        'user_id' => $user_id,
                        'net_user_id' => $net_user_id,
                        'net_user_name' => $net_user_name,
                        'shop_id' => $shop_id,
                        'times' => $scan_times,
                        'warning_type' => $warning_type
                    ]);
                }
            }
        }
    }

    /**
     * 第一次扫码用户发送给NET做标签
     * @param $net_user_id
     * @param $net_user_name
     * @param $shop_id
     * @param $user_id
     * @param $user_name
     * @param $code_id
     * @param $code
     * @return bool
     */
    public static function addNewUserCategory($type, $net_user_id, $net_user_name, $shop_id, $user_id, $user_name, $code_id, $code)
    {
        $paramArr = [
            'UserId' => $net_user_id,
            'ShopId' => $shop_id,
        ];
        $param = json_encode($paramArr);
        $url = config('custom.net_url').config('custom.scan_code_add_category_api_url').'?data='.$param;
        $data = get_api_data($url, 'get', 'xml');
        $netApiLog = new NetApiLog();
        $netApiLog->api_id = 1;
        $netApiLog->user_id = $user_id;
        $netApiLog->shop_id = $shop_id;
        $netApiLog->code_id = $code_id;
        $netApiLog->net_user_id = $net_user_id;
        $netApiLog->net_user_name = $net_user_name;
        $netApiLog->role = 'user';
        $reason = '';
        $api_status = '';
        if ($data['Msg'] == 'ok') {
            $netApiLog->status = 'success';
            $api_status = '成功';
        } elseif ($data['Msg'] == 'no') {
            $netApiLog->status = 'failed';
            $api_status = '失败';
            $reason = '原因是' . $data['Info'];
        }
        if ('user' == $type) {
            $string = '服务员' . $user_name;
        } elseif ('net_user' == $type) {
            $string = '用户' . $net_user_name;
        }
        $netApiLog->comment = $string . '扫码'. $code . $api_status . $reason;
        return $netApiLog->save();
    }

    /**
     * 通过微信unionid判断是否是同一个人扫码，如是同一个添加扫码预警记录
     * @param $user_id
     * @param null $net_wechat_unionid
     * @param $shop_id
     * @param $net_user_id
     * @param $net_user_name
     * @param $net_scan_times
     * @return bool
     */
    public static function addScanWarningLog($user_id, $net_wechat_unionid = null, $shop_id, $net_user_id, $net_user_name, $net_scan_times)
    {
        if (! ($net_wechat_unionid && $user_id)) {
            return false;
        }

        $user = User::find($user_id);
        if (! $user) {
            return false;
        }

        if ($user->wechat_unionid == $net_wechat_unionid) {
            ScanWarning::create([
                'user_id' => $user_id,
                'shop_id' => $shop_id,
                'net_user_id' => $net_user_id,
                'net_user_name' => $net_user_name,
                'times' => $net_scan_times,
                'warning_type' => 5,
            ]);
        }

        return false;
    }

}