<?php

namespace App\Http\Controllers\API;

use App\FundingPoolLog;
use App\Http\Requests;
use App\SMSLog;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application as WechatApp;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @SWG\Tag(name="Test", description="测试")
 */
class TestController extends APIController
{

	/**
     * @SWG\Post(
     *     path="/test/withdraw",
     *     tags={"Test"},
     *     summary="测试提现",
     *     @SWG\Parameter(name="openid", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="amount", in="formData", required=false, type="integer"),
     *     @SWG\Response(response="200", description="服务员数据")
     * )
     * @param Request $request
     * @return JsonResponse
     */
	function withdraw(Request $request, WechatApp $wechat) {
		$this->validate($request, [
            'openid' => 'required',
            'amount' => 'required|integer|min:100|max:20000',
        ], [
            'openid.required' => '1000001|openid不能为空',
            'amount.required' => '1000002|提现金额不能为空',
            'amount.integer' => '1000003|提现金额需大于等于1元,小于等于200元',
            'amount.min' => '1000004|提现金额需大于等于1元,小于等于200元',
            'amount.max' => '1000005|提现金额需大于等于1元,小于等于200元',
        ]);

        $merchantPayData = [
            'partner_trade_no' => "mikcarytest". date("YmdHis"),
            'openid' => $request->input('openid'),
            'check_name' => 'NO_CHECK',
            're_user_name'=>'NO_CHECK',
            'amount' => $request->input('amount'),
            'desc' => '米客提现红包',
            'spbill_create_ip' => '192.168.0.1',
        ];

        $result = $wechat->merchant_pay->send($merchantPayData);

        if ($result->get('result_code') != 'SUCCESS') {
            return new JsonResponse($this->buildErrorResponse('415|微信支付失败,稍后重试'), 500);
        }

        return new JsonResponse([]);
	}

    /**
     * @SWG\Get(
     *     path="/test/noti",
     *     tags={"Test"},
     *     summary="测试智能提醒",
     *     @SWG\Response(response="200", description="OK")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    function checkNotification(Request $request) {
        $latestFundingPoolLog = FundingPoolLog::orderBy('created_at', 'desc')->first();
        $balance = $latestFundingPoolLog->balance;

        $fundingPoolThreshold = Settings::get('notification.threshold.funding_pool_balance', 10000);

        if ($fundingPoolThreshold * 100 > $balance) {
            $notiCount = SMSLog::where('type', 'admin_notify_funding_pool')
                ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->count();

            if ($notiCount == 0) {
                $phones_str = Settings::get('notification.phones', '[]');
                $phones = json_decode($phones_str);

                var_dump($phones);
                var_dump($balance);
            }
        }
    }
}