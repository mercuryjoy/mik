<?php

namespace App\Http\Controllers\API;

use App\Code;
use App\Common\Common;
use App\Events\ScanEvent;
use App\Helpers\Contracts\SMSContract;
use App\ScanLog;
use App\User;
use App\Shop;
use App\UserMoneyLog;
use App\UserPointLog;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

/**
 * @SWG\Tag(name="Scan Log", description="扫码记录")
 */
class ScanLogController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/scans/logs",
     *     tags={"Scan Log"},
     *     summary="终端的核销记录",
     *     @SWG\Parameter(name="page", in="query", required=false, type="string", description="页码"),
     *     @SWG\Parameter(name="pageSize", in="query", required=false, type="string", description="每页显示数量"),
     *     @SWG\Response(response="301", description="帐号未通过审核不能查看"),
     *     @SWG\Response(response="400", description="服务员没有所属终端"),
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user;
        if ($user->status == 'pending') {
            return new JsonResponse($this->buildErrorResponse('301|帐号未通过审核不能查看'), 400);
        }

        if (!$user->shop_id) {
            return new JsonResponse($this->buildErrorResponse('400|服务员没有所属终端'), 400);
        }
        $pageSize = $request->pageSize ? $request->pageSize : 20;

        $data = ScanLog::with('user')
            ->where('type', 'scan_coupon')
            ->where('shop_id', $user->shop_id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($pageSize);
        $scanLog = $data->toArray();
        return new JsonResponse($scanLog['data']);
    }

    /**
     * @SWG\Post(
     *     path="/scans",
     *     tags={"Scan Log"},
     *     summary="上传扫描二维码信息",
     *     @SWG\Parameter(name="code", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="version", in="formData", required=false, type="string"),
     *     @SWG\Response(response="200", description="上传扫描二维码信息成功"),
     *     @SWG\Response(response="400", description="客户端请求有误"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     * @param Request $request
     * @param SMSContract $sms
     * @return JsonResponse
     */
    public function store(Request $request, SMSContract $sms)
    {
        // 0. check user status
        $user = $request->user;
        if ($user->status == 'pending') {
            return new JsonResponse($this->buildErrorResponse('301|帐号未通过审核不能扫码'), 400);
        }

        // 1. validate input
        $this->validate($request, [
            'code' => 'required',
        ], [
            'code.required' => '302|二维码不能为空',
        ]);

        // 2. find out the draw rule
        $user->load('shop', 'shop.distributor');

        $codeInfo = explode('&', $request->input('code'));
        $version = $request->input('version');
        if (count($codeInfo) == 2) {
            if ($request->has('version') && $version) {
                if ($codeInfo[1] == 1) {      // 确定为优惠券
                    $verify_res = Common::verifyCoupon($codeInfo[0], $user->id, $user->name, $user->shop_id, $user->shop->name);
                    if ($verify_res['code'] == 200) {
                        return new JsonResponse(['code_id' => 0, 'user_id' => 0, 'shop_id' => 0, 'luck_id' => 0, 'money' => 0, 'point' => 0, 'is_coupon' => true, 'message' => '核销成功',]);
                    } elseif ($verify_res['code'] == 400) {
                        return new JsonResponse($this->buildErrorResponse('304|'.$verify_res['message']), 400);
                    }
                }
            }

            return new JsonResponse($this->buildErrorResponse('304|版本号有误'), 400);
        }

        // 正常二维码
        // 3. check code valid
        $code = Code::where('code', $request->input('code'))->first();

        if ($code == null) {
            return new JsonResponse($this->buildErrorResponse('303|二维码不存在'), 400);
        } else if ($code->batch->status != 'normal' || $code->batch->type == 'miniapp') {
            return new JsonResponse($this->buildErrorResponse('304|该二维码目前无法扫描'), 400);
        } else if ($code->scan_log_id > 0) {
            return new JsonResponse($this->buildErrorResponse('304|你又调皮了，该二维码已被扫过'), 400);
        }

        $shop = $user->shop;

        if (! $user->shop_id) {
            return new JsonResponse($this->buildErrorResponse('304|您暂时没有所属终端，请联系管理员！'), 400);
        }

        $shop = Shop::withTrashed()->find($user->shop_id);

        $drawRule = null;
        $point_award = 0;
        $ownerMoneyAward = 0;
        if (! $shop) {    // 终端规则
            return new JsonResponse($this->buildErrorResponse('304|您的所属终端不存在，请联系管理员！'), 400);
        }

        if ($shop->trashed()) {
            return new JsonResponse($this->buildErrorResponse('304|您归属的终端被禁用，请联系销售员开启后重试！'), 400);
        }

        $activityRule = Common::getShopActivity($shop->id);
        if ($activityRule['code'] == 200) {
            $drawRule = $activityRule['data']['red_envelope'];
            $point_award = $activityRule['data']['point'];
            $ownerMoneyAward = $activityRule['data']['money'] * 100;
        }

        if ($drawRule == null) {
            return new JsonResponse($this->buildErrorResponse('305|未找到可使用的抽奖规则'), 500);
        }

        // 4. draw money
        $luck_id = mt_rand(1, 100);

        $rules = json_decode($drawRule);
        if (!is_array($rules)) {
            return new JsonResponse($this->buildErrorResponse('305|未找到可使用的抽奖规则'), 500);
        }

        $upbound = 0;
        $min = $max = -1;
        foreach ($rules as $rule) {
            $upbound += $rule->percentage;
            if ($upbound >= $luck_id) {
                $min = $rule->min * 100;
                $max = $rule->max * 100;
                break;
            }
        }

        if ($max < 0) {
            return new JsonResponse($this->buildErrorResponse('305|未找到可使用的抽奖规则'), 500);
        }

        $money_award = mt_rand($min, $max);
        // 5. add scan log
        $scan_log = ScanLog::create([
            'type' => 'scan_prize',
            'code_id' => $code->id,
            'user_id' => $user->id,
            'shop_id' => $user->shop_id,
            'luck_id' => $luck_id,
            'money' => $money_award,
            'point' => $point_award,
            'salesman_id' => ($user->shop && $user->shop->salesman_id) ? $user->shop->salesman_id : 0,
            'distributor_id' => ($user->shop && $user->shop->distributor_id) ? $user->shop->distributor_id : 0,
        ]);

        $code->scan_log_id = $scan_log->id;
        $code->save();

        // 6. update user balance (money and point)
        $request->user->money_balance += $money_award;
        $request->user->point_balance += $point_award;
        $request->user->save();

        // 7. update funding pool
        UserMoneyLog::create([
            'type' => 'scan_prize',
            'amount' => $money_award,
            'user_id' => $user->id,
            'scan_log_id' => $scan_log->id,
            'comment' => 'Scan ' . $scan_log->id,
        ]);

        UserPointLog::create([
            'type' => 'scan_prize',
            'amount' => $point_award,
            'user_id' => $user->id,
            'scan_log_id' => $scan_log->id,
            'comment' => 'Scan ' . $scan_log->id,
        ]);

        // 8. Scan Event to trigger notification
        Event::fire(new ScanEvent($scan_log, $sms));

        // 二维码为活动二维码
        $waiterMoneyAward = intval(Settings::get('scan.money_to_waiter_first_scan', 1)) * 100;
        $waiterPointAward = intval(Settings::get('scan.point_to_waiter_first_scan', 1));

        $owner = null;
        if ($shop && $shop->owner_id > 0) {
            $owner = User::find($shop->owner_id);
        }

        if ($code->batch->type && $code->batch->type == 'activity') {

            $userScanLogObj = new ScanLog();

            $scanLog = ScanLog::where('code_id', $code->id)
                ->where('type', 'scan_send_money_activity')
                ->where('waiter_id', 0)
                ->where('user_id', 0)
                ->where('net_user_id', '>', 0)
                ->first();

            // 服务员没扫
            if (!$scanLog) {
                $userScanLogObj->code_id = $code->id;
                $userScanLogObj->shop_id = $user->shop_id ? $user->shop_id : '';
                $userScanLogObj->user_id = $user->id;
                $userScanLogObj->type = 'scan_send_money_activity';
                $userScanLogObj->save();
            } elseif ($scanLog->net_user_times == 1) { // 服务员没扫，第一次的用户扫了
                $scanLog->shop_id = $user->shop_id ? $user->shop_id : '';
                $scanLog->user_id = $user->id;
                $scanLog->money = $waiterMoneyAward;
                $scanLog->point = $waiterPointAward;
                $scanLog->save();

                if ($waiterMoneyAward > 0) {
                    // add money log
                    UserMoneyLog::create([
                        'amount' => $waiterMoneyAward,
                        'user_id' => $user->id,
                        'scan_log_id' => $scanLog->id,
                        'type' => 'user_scan_to_waiter',
                        'comment' => 'normal user first scan send money log_id:'.$scanLog->id,
                    ]);

                    $request->user->money_balance += $waiterMoneyAward;
                    $request->user->save();
                }

                if ($waiterPointAward > 0) {
                    // add money log
                    UserPointLog::create([
                        'amount' => $waiterPointAward,
                        'user_id' => $user->id,
                        'scan_log_id' => $scanLog->id,
                        'type' => 'user_scan_to_waiter',
                        'comment' => 'normal user first scan send point log_id:'.$scanLog->id,
                    ]);

                    $request->user->point_balance += $waiterPointAward;
                    $request->user->save();
                }

                // add scan warning
                Common::addScanWarningLog($user->id, $scanLog->net_wechat_unionid, $user->shop_id, $scanLog->net_user_id, $scanLog->net_user_name, $scanLog->net_user_times);

                // 发送给NET创建标签
                Common::addNewUserCategory('user', $scanLog->net_user_id, $scanLog->net_user_name, $user->shop_id, $user->id, $user->name, $code->id, $request->code);
            } elseif ($scanLog->net_user_times > 1) { // 服务员没扫，不是第一次的用户扫了
                $scanLog->user_id = $user->id;
                $scanLog->shop_id = $shop ? $shop->id : '';
                $scanLog->save();

//                // add scan warning
                Common::addScanWarningLog($user->id, $scanLog->net_wechat_unionid, $user->shop_id, $scanLog->net_user_id, $scanLog->net_user_name, $scanLog->net_scan_times);
            }
        }

        // 有店长给店长发红包
        if ($owner !== null) {
            $userScanLogObj = ScanLog::create([
                'code_id' => $code->id,
                'shop_id' => $user->shop_id ? $user->shop_id : '',
                'user_id' => $shop->owner_id,
                'waiter_id' => $user->id,
                'money' => $ownerMoneyAward,
                'type' => 'scan_send_money_activity',
            ]);

            if ($ownerMoneyAward > 0) {
                if ($user->id !== $owner->id) {
                    $ownerObj = User::find($owner->id);
                    $ownerObj->money_balance += $ownerMoneyAward;
                    $ownerObj->save();
                } else {    // 服务员就是店长
                    $request->user->money_balance += $ownerMoneyAward;
                    $request->user->save();
                }

                UserMoneyLog::create([
                    'amount' => $ownerMoneyAward,
                    'user_id' => $owner->id,
                    'scan_log_id' => $userScanLogObj->id,
                    'type' => 'waiter_scan_to_owner',
                    'comment' => 'waiter scan send money to owner log_id:'.$userScanLogObj->id,
                ]);
            }
        }

        // 8. send scan log back
        $successData = array_merge(['is_coupon' => false, 'message' => '扫码成功',], $scan_log->toArray());
        return new JsonResponse($successData);
    }

    /**
     * @SWG\Get(
     *     path="/users/{user}/scans",
     *     tags={"Scan Log"},
     *     summary="获取服务员所有扫码记录",
     *     @SWG\Parameter(name="user", in="path", required=true, type="string"),
     *     @SWG\Response(response="200", description="扫码记录获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function listByUser($user_id, Request $request) {
        if ($user_id == "@me") {
            $user_id = $request->user->id;
        }

        $logs = ScanLog::where('user_id', $user_id)
            ->where('type', 'scan_prize')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return new JsonResponse($logs->toArray());
    }

    /**
     * @SWG\Get (
     *     path = "/scans/rank",
     *     tags = {"Scan Log"},
     *     summary = "比一比",
     *     @SWG\Parameter(name="datefrom", in="query", description="e.g. 2015-12-10", required=false, type="string"),
     *     @SWG\Parameter(name="dateto", in="query", description="e.g. 2016-12-10", required=false, type="string"),
     *     @SWG\Parameter(name="areaid", in="query", required=false, type="integer"),
     *     @SWG\Parameter(name="count", in="query", required=false, type="integer"),
     *     @SWG\Response(response="200", description="比一比记录获取成功"),
     * )
     */
    public function rank(Request $request) {
        // params
        $this->validate($request, [
            "datefrom" => "date_format:Y-m-d",
            "dateto" => "date_format:Y-m-d|before:tomorrow",
            "areaid" => "exists:areas,id",
            "count" => "integer|min:1",
        ], [
            "datefrom.*" => "311|开始时间,结束时间有误",
            "dateto.*" => "311|开始时间,结束时间有误",
            "areaid.*" => "312|地区代码不存在",
            "count.*" => "313|返回条目数量不正确",
        ]);

        try {
            $start = Carbon::parse($request->input("datefrom", "fault"))->startOfDay();
        } catch (\Exception $e) {
            $start = Carbon::minValue();
        }

        try {
            $end = Carbon::parse($request->input("dateto", "fault"))->endOfDay();
        } catch (\Exception $e) {
            $end = Carbon::today()->endOfDay();
        }

        $count = intval($request->input("count", "10"));
        $area_id = $request->input("areaid");

        $logs = ScanLog::with("user")
            ->whereBetween('created_at', [$start, $end])
            ->where('type', 'scan_prize')
            ->area($area_id)
            ->groupBy('user_id')
            ->selectRaw('user_id, count(*) as count, sum(point) as sum, sum(money) as total_money')
            ->orderBy('sum', 'desc')
            ->limit($count)
            ->get();

        $arr = $logs->toArray();
        foreach($arr as $i => $log) {
            $log["sum"] = intval($log["sum"]);
            $log["total_money"] = intval($log["total_money"]);
            $arr[$i] = $log;
        }

        return new JsonResponse($arr);
    }
}
