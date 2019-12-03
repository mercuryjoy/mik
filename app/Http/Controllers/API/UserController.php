<?php

namespace App\Http\Controllers\API;

use App\Events\WithdrawEvent;
use App\FundingPoolLog;
use App\Helpers\Contracts\SMSContract;
use App\ScanLog;
use App\User;
use App\UserMoneyLog;
use App\UserPointLog;
use App\UserShopChangeLog;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application as WechatApp;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;

/**
 * @SWG\Tag(name="User", description="服务员")
 */
class UserController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/users/@me",
     *     tags={"User"},
     *     summary="获取当前服务员信息",
     *     @SWG\Response(response="200", description="服务员数据",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="name"),
     *              @SWG\Property(type="string", property="gender"),
     *              @SWG\Property(type="string", property="telephone"),
     *              @SWG\Property(type="integer", property="shop_id"),
     *              @SWG\Property(type="string", property="deleted_at"),
     *              @SWG\Property(type="string", property="created_at"),
     *              @SWG\Property(type="string", property="updated_at"),
     *          )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function showMe(Request $request) {
        $user = $request->user;
        $user->load('shop');
        $user->is_owner = false;
        $user->is_set_pay_password = $user->pay_password ? 1 : 0;
        if ($user->shop && $user->id == $user->shop->owner_id) {
            $user->is_owner = true;
        }
        return new JsonResponse($user->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/users/@me/statics",
     *     tags={"User"},
     *     summary="获取当前服务员统计信息",
     *     @SWG\Parameter(name="from", in="query", required=false, type="string"),
     *     @SWG\Parameter(name="to", in="query", required=false, type="string"),
     *     @SWG\Response(response="200", description="服务员统计数据",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="name"),
     *          )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function showMeStatics(Request $request) {
        try {
            $from = Carbon::parse($request->input('from'));
        } catch(\Exception $e) {
            $from = Carbon::minValue();
        }

        try {
            $to = Carbon::parse($request->input('to'));
        } catch(\Exception $e) {
            $to = Carbon::maxValue();
        }

        $scanMoneyPoint = ScanLog::where('user_id', $request->user->id)
            ->whereBetween('created_at', [$from, $to->endOfDay()])
            ->orderBy('created_at', 'desc')->get();

        $scanTimes = ScanLog::where('user_id', $request->user->id)
            ->where('type', 'scan_prize')
            ->whereBetween('created_at', [$from, $to->endOfDay()])
            ->orderBy('created_at', 'desc')->get();

        $statics = [
            'scan_count' => $scanTimes->count(),
            'scan_point' => $scanMoneyPoint->sum('point'),
            'scan_money' => $scanMoneyPoint->sum('money'),
            'money_balance' => intval($request->user->money_balance),
            'point_balance' => intval($request->user->point_balance),
        ];

        return new JsonResponse($statics);
    }

    /**
     * @SWG\Put(
     *     path="/users/@me",
     *     tags={"User"},
     *     summary="修改当前服务员信息",
     *     @SWG\Parameter(name="name", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="gender", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="shop_id", in="formData", required=false, type="integer"),
     *     @SWG\Parameter(name="telephone", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="wechat_openid", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="wechat_unionid", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="wechat_name", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="wechat_avatar", in="formData", required=false, type="string"),
     *     @SWG\Response(response="200", description="用户数据",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="name"),
     *              @SWG\Property(type="string", property="gender"),
     *              @SWG\Property(type="string", property="telephone"),
     *              @SWG\Property(type="integer", property="shop_id"),
     *              @SWG\Property(type="string", property="deleted_at"),
     *              @SWG\Property(type="string", property="created_at"),
     *              @SWG\Property(type="string", property="updated_at"),
     *              @SWG\Property(type="string", property="token"),
     *              @SWG\Property(type="string", property="wechat_unionid"),
     *              @SWG\Property(type="string", property="wechat_name"),
     *              @SWG\Property(type="string", property="wechat_avatar")
     *          )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function storeMe(Request $request) {
        $user = $request->user;
        $originShopId = $user->shop_id;

        $this->validate($request, [
            'name' => 'max:30|min:2',
            'gender' => 'in:male,female',
            'shop_id' => 'exists:shops,id',
            'telephone' => 'regex:' . config('custom.telephone_regex') . '|unique:users,telephone' . ($user ? ',' . $user->id : ''),
        ], [
            'name.*' => '401|姓名为2-30位中英文字符',
            'gender.*' => '402|性别输入有误',
            'shop_id.*' => '403|终端未找到',
            'telephone.regex' => '405|手机号码格式不正确',
            'telephone.unique' => '406|手机号码已被其他服务员使用',
        ]);

        if ($request->shop_id && $originShopId != null && $originShopId != $request->shop_id) {
            return new JsonResponse($this->buildErrorResponse('400|不支持修改终端'), 500);
        }

        $wechat_openid = $request->input('wechat_openid');
        if ($wechat_openid) {
            if ($user->wechat_openid) {
                return new JsonResponse($this->buildErrorResponse('407|您已绑定过微信账号，请解绑后重试'), 400);
            }

            $bind_count = User::where('wechat_openid', $wechat_openid)->count();
            if ($bind_count > 0) {
                return new JsonResponse($this->buildErrorResponse('408|该微信号已绑定其他账号'), 400);
            }
        }

        $update_fields = ['name', 'gender', 'shop_id', 'area_id', 'telephone', 'wechat_openid', 'wechat_unionid', 'wechat_name', 'wechat_avatar'];
        $isUpdated = $user->update(array_filter($request->only($update_fields)));
        if ($isUpdated) {
            if ($originShopId != null && $originShopId != $user->shop_id) {
                UserShopChangeLog::create([
                    'user_id' => $user->id,
                    'before_shop_id' => $originShopId,
                    'after_shop_id' => $user->shop_id
                ])->save();
            }

            return new JsonResponse($user);
        }

        return new JsonResponse($this->buildErrorResponse('400|更新个人信息失败'), 400);
    }

    public function unbindWechat(Request $request)
    {
        $user = $request->user;
        if (! $user->wechat_openid) {
            return new JsonResponse($this->buildErrorResponse('201|您暂未绑定微信'), 400);
        }

        $user->wechat_openid = '';
        $user->wechat_avatar = '';
        $user->wechat_name = '';
        $result = $user->save();

        if (! $result) {
            return new JsonResponse($this->buildErrorResponse('202|解绑失败'), 400);
        }

        return new JsonResponse($user);
    }

    /**
     * @SWG\Post(
     *     path="/withdraw",
     *     tags={"User"},
     *     summary="用户提现",
     *     @SWG\Parameter(name="amount", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="password", in="formData", required=true, type="integer"),
     *     @SWG\Response(response="200", description="提现记录")
     *     )
     * )
     *
     * @param Request $request
     * @param WechatApp $wechat
     * @param SMSContract $sms
     * @return JsonResponse
     */
    public function withdraw(Request $request, WechatApp $wechat, SMSContract $sms) {
        $status = Settings::get('app.withdraw_api_service.status', 'on');

        if ($status == 'off') {
            return new JsonResponse($this->buildErrorResponse('400|钱包提现功能暂时无法使用'), 500);
        }

        $user = $request->user;
        $version = $request->version;

        $this->validate($request, [
            'amount' => 'required|integer|min:100|max:20000',
        ], [
            'amount.required' => '411|提现金额不能为空',
            'amount.integer' => '412|提现金额需大于等于1元,小于等于200元',
            'amount.min' => '412|提现金额需大于等于1元,小于等于200元',
            'amount.max' => '412|提现金额需大于等于1元,小于等于200元',
        ]);

        if (! empty($version)) {
            $this->validate($request, [
                'pay_password' => 'required|digits:6'
            ], [
                'pay_password.required' => '416|密码不能为空',
                'pay_password.digits' => '417|密码必须为6个数字',
            ]);

            
            if (! $user->pay_password) {
                return new JsonResponse($this->buildErrorResponse('419|您暂未设置提现密码，请设置后重试'), 400);
            }

            $pay_password = $request->input('pay_password');
            if (! password_verify($pay_password, $user->pay_password)) {
                return new JsonResponse($this->buildErrorResponse('418|密码错误'), 400);
            }
        }

        $amount = intval($request->input('amount'));
        if ($user->money_balance < $amount) {
            return new JsonResponse($this->buildErrorResponse('413|钱包余额不足'), 500);
        }

        if (empty($user->wechat_openid)) {
            return new JsonResponse($this->buildErrorResponse('414|未关联微信'), 500);
        }

        $mch_billno = 'mik' . str_pad($user->id, 8, '0', STR_PAD_LEFT) . date("YmdHis");
        $merchantPayData = [
            'partner_trade_no' => $mch_billno,
            'openid' => $user->wechat_openid,
            'check_name' => 'NO_CHECK',
            're_user_name'=>'NO_CHECK',
            'amount' => $amount,
            'desc' => '米客提现红包',
            'spbill_create_ip' => '192.168.0.1',
        ];

        $result = $wechat->merchant_pay->send($merchantPayData);


        if ($result->get('result_code') != 'SUCCESS') {
            return new JsonResponse($this->buildErrorResponse('415|微信支付失败,稍后重试'), 500);
        }

        $log = UserMoneyLog::create([
            'type' => 'withdraw',
            'amount' => -$amount,
            'user_id' => $user->id,
            'comment' => 'Withdraw, Wechat Payment Bill Number:' . $mch_billno,
        ]);

        $user->money_balance -= $amount;
        $user->save();

        // funding pool
        $latestFundingPoolLog = FundingPoolLog::orderBy('created_at', 'desc')->first();
        $fundingPoolBalance = -$amount;
        if ($latestFundingPoolLog != null) {
            $fundingPoolBalance += $latestFundingPoolLog->balance;
        }
        FundingPoolLog::create([
            'type' => 'user_withdraw',
            'amount' => - $amount,
            'balance' => $fundingPoolBalance,
            'user_id' => $user->id,
            'comment' => 'Withdraw, Wechat Payment Bill Number:' . $mch_billno,
        ]);

        Event::fire(new WithdrawEvent($sms));

        return new JsonResponse($log->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/exchange",
     *     tags={"User"},
     *     summary="将红包余额转换为积分",
     *     @SWG\Parameter(name="amount", in="formData", required=false, type="integer"),
     *     @SWG\Response(response="200", description="转换成功")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exchange(Request $request) {
        $this->validate($request, [
            'amount' => 'required|integer|min:100',
        ], [
            'amount.required' => '421|转换金额不能为空',
            'amount.*' => '422|转换金额需大于等于1元'
        ]);

        $user = $request->user;
        $amount = intval($request->input('amount'));
        if ($amount % 100 != 0) {
            return new JsonResponse($this->buildErrorResponse('423|转换金额需为1元的整数倍'), 500);
        }

        if ($user->money_balance < $amount) {
            return new JsonResponse($this->buildErrorResponse('424|钱包余额不足'), 500);
        }

        $exchange_rate = intval(Settings::get('app.money_to_point_exchange_rate', '100'));

        $money_amount = -$amount;
        $point_amount = $amount / 100 * $exchange_rate;

        UserMoneyLog::create([
            'type' => 'exchange_to_point',
            'amount' => $money_amount,
            'user_id' => $user->id,
            'comment' => 'Rate: 1元 = ' . $exchange_rate . '积分',
        ]);

        UserPointLog::create([
            'type' => 'exchange_from_money',
            'amount' => $point_amount,
            'user_id' => $user->id,
            'comment' => 'Rate: 1元 = ' . $exchange_rate . '积分',
        ]);

        $user->money_balance += $money_amount;
        $user->point_balance += $point_amount;
        $user->save();

        return new JsonResponse([]);
    }

    /**
     * @SWG\Get(
     *     path="/wallet/log",
     *     tags={"User"},
     *     summary="用户钱包变动记录",
     *     @SWG\Parameter(name="version", in="query", required=false, type="string"),
     *     @SWG\Response(response="200", description="用户钱包变动记录")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function walletLog(Request $request) {
        $user = $request->user;
        if ($request->has('version')) {
            $pageSize = $request->pageSize ? $request->pageSize : 10;
            $fields = ['uml.type', 'uml.user_id', 'uml.amount', 'uml.created_at', 'sl.waiter_id', 'sl.net_user_name'];
            $moneyObj = UserMoneyLog::select($fields)
                ->from('user_money_logs as uml')
                ->where('uml.user_id', $user->id)
                ->leftJoin('scan_logs as sl', 'uml.scan_log_id', '=', 'sl.id')
                ->orderBy('uml.created_at', 'desc')
                ->orderBy('uml.id', 'desc')
                ->paginate($pageSize);

            $dataArr = $moneyObj->toArray();

            $data = [];
            if ($dataArr['data'] && count($dataArr['data']) > 0 ) {
                foreach ($dataArr['data'] as $item) {
                    $scanUserName = '';
                    if ($item['type'] == 'user_scan_to_waiter') {   // 用户扫码发红包给服务员
                        $scanUserName = $item['net_user_name'];
                    } elseif ($item['type'] == 'waiter_scan_to_owner') {    // 服务员扫码发红包给店长
                        $waiter = User::find($item['waiter_id']);
                        $scanUserName = $waiter->name;
                    }

                    $data[] = [
                        'type' => $item['type'],
                        'amount' => $item['amount'],
                        'created_at' => $item['created_at'],
                        'scan_user_name' => $scanUserName,
                    ];
                }
            }
            return new JsonResponse($data);
        }

        $logs = UserMoneyLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return new JsonResponse($logs->toArray());
    }
}
