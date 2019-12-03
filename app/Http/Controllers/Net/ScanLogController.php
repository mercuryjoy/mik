<?php

namespace App\Http\Controllers\Net;

use App\Code;
use App\Common\Common;
use App\ScanLog;
use App\User;
use App\UserMoneyLog;
use App\UserPointLog;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;

class ScanLogController extends NetController
{
    // 提供.net接口活动二维码用户扫之后给服务员发红包，插入红包扫码记录，写明原因，后台设置用户首次扫码给服务员红包金额
    public function scans(Request $request)
    {
        // 1. validate input
        $this->validate($request, [
            'user_id' => 'required|integer',
            'user_name' => 'required|min:1|max:20',
            'code' => 'required|regex:/^[a-z0-9]{9,14}$/|exists:codes',
        ], [
            'user_id.required' => '400|用户ID不能为空',
            'user_id.integer' => '400|用户ID必须为整数',
            'user_name.required' => '400|用户名称不能为空',
            'user_name.min' => '400|用户名称长度在1-20个字符',
            'user_name.max' => '400|用户名称长度在1-20个字符',
            'code.required' => '400|二维码不能为空',
            'code.regex' => '400|二维码格式不正确',
            'code.exists' => '400|二维码不存在'
        ]);

        // 2. check code valid
        $code = Code::where('code', $request->input('code'))
                    ->first();
        if (!$code) {
            return $this->jsonReturn(400, '该二维码信息获错误');
        } elseif (! in_array($code->batch->type, ['activity', 'miniapp'])) {
            return $this->jsonReturn(400, '您的二维码不支持抽奖活动');
        } elseif ($code->batch->status != 'normal') {
            return $this->jsonReturn(400, '该二维码目前无法扫描');
        } elseif ($code->user_scan_log_id > 0) {
            return $this->jsonReturn(400, '该二维码已被扫过');
        }

        $type = 'scan_send_money_activity';
        if ($code->batch->type == 'miniapp') {
            $type = 'miniapp_user_scan';
        }

        $net_scan_times = ScanLog::where('net_user_id', $request->user_id)
            ->where('type', $type)
            ->where('waiter_id', 0)
            ->count();
        // 3. check code scan log
        $scan_log = ScanLog::where('code_id', $code->id)
            ->where('type', 'scan_send_money_activity')
            ->where('waiter_id', 0)
            ->where('net_user_id', 0)
            ->where('user_id', '>', 0)
            ->first();
        $moneyAward = intval(Settings::get('scan.money_to_waiter_first_scan', 1)) * 100;
        $pointAward = intval(Settings::get('scan.point_to_waiter_first_scan', 1));
        $scan_log_obj = new ScanLog();

        $net_user_id = $request->input('user_id');
        $net_user_name = $request->input('user_name');
        $net_wechat_unionid = $request->input('net_wechat_unionid');
        $shop_id = $scan_log ? $scan_log->shop_id : '';
        $user_id = $scan_log ? $scan_log->user_id : '';

        // 用户第一次扫
        if ($net_scan_times == 0) {
            if (!$scan_log) {    // 服务员没扫
                // add normal user scan log
                $scan_log_obj->code_id = $code->id;
                $scan_log_obj->money = 0;
                $scan_log_obj->net_user_id = $net_user_id;
                $scan_log_obj->net_user_name = $net_user_name;
                $scan_log_obj->net_user_times = $net_scan_times + 1;
                $scan_log_obj->net_wechat_unionid = $net_wechat_unionid;
                $scan_log_obj->type = $type;
                $scan_log_obj->save();

                // alert code normal user cannot scan
                $code->user_scan_log_id = $scan_log_obj->id;
                $code->save();
                return $this->jsonReturn(200, '您是新用户，扫码成功');
            } else {   // 服务员扫了，用户没扫
                // update normal user_id to user scan log
                $scan_log->net_user_id = $net_user_id;
                $scan_log->net_user_name = $net_user_name;
                $scan_log->money = $moneyAward;
                $scan_log->point = $pointAward;
                $scan_log->net_user_times = $net_scan_times + 1;
                $scan_log->net_wechat_unionid = $net_wechat_unionid;
                $scan_log->save();

                // update code normal user cannot scan
                $code->user_scan_log_id = $scan_log->id;
                $code->save();

                if ($moneyAward > 0) {
                    // UPDATE USER'S MONEY_BALANCE
                    $waiter = User::find($user_id);
                    $waiter->money_balance += $moneyAward;
                    $waiter->save();

                    // add send money log
                    UserMoneyLog::create([
                        'type' => 'user_scan_to_waiter',
                        'amount' => $moneyAward,
                        'user_id' => $user_id,
                        'scan_log_id' => $scan_log->id,
                        'comment' => 'normal user first scan send money to waiter log_id:'.$scan_log->id,
                    ]);
                }

                if ($pointAward > 0) {
                    // UPDATE USER'S MONEY_BALANCE
                    $waiter = User::find($user_id);
                    $waiter->point_balance += $pointAward;
                    $waiter->save();

                    // add send money log
                    UserPointLog::create([
                        'type' => 'user_scan_to_waiter',
                        'amount' => $pointAward,
                        'user_id' => $user_id,
                        'scan_log_id' => $scan_log->id,
                        'comment' => 'normal user first scan send point to waiter log_id:'.$scan_log->id,
                    ]);
                }
                

                // add scan warning
                if ($request->has('net_wechat_unionid') && $net_wechat_unionid) {
                    Common::addScanWarningLog($user_id, $net_wechat_unionid, $scan_log->shop_id, $net_user_id, $net_user_name, $net_scan_times + 1);
                }

                // 发送给NET创建标签
                Common::addNewUserCategory('net_user', $net_user_id, $net_user_name, $shop_id, $user_id, '', $code->id, $request->code);

                return $this->jsonReturn(200, '您是新用户，扫码成功');
            }
        } elseif ($net_scan_times > 0) {     // 用户不是第一次扫
            if (! $scan_log) {    // 服务员没扫
                // update normal user_id to user scan log
                $scan_log_obj->code_id = $code->id;
                $scan_log_obj->net_user_id = $net_user_id;
                $scan_log_obj->net_user_name = $net_user_name;
                $scan_log_obj->money = 0;
                $scan_log_obj->net_user_times = $net_scan_times + 1;
                $scan_log_obj->net_wechat_unionid = $net_wechat_unionid;
                $scan_log_obj->type = $type;
                $scan_log_obj->save();

                // update code normal user cannot scan
                $code->user_scan_log_id = $scan_log_obj->id;
                $code->save();
                return $this->jsonReturn(200, '您是老用户,二维码可用，扫码成功');
            } else {   // 服务员扫了，用户没扫
                // update normal user_id to user scan log
                $scan_log->net_user_id = $net_user_id;
                $scan_log->net_user_name = $net_user_name;
                $scan_log->money = 0;
                $scan_log->net_user_times = $net_scan_times + 1;
                $scan_log_obj->net_wechat_unionid = $net_wechat_unionid;
                $scan_log->save();

                // update code normal user cannot scan
                $code->user_scan_log_id = $scan_log->id;
                $code->save();

                // add scan warning
                if ($request->has('net_wechat_unionid') && $net_wechat_unionid) {
                    Common::addScanWarningLog($scan_log->user_id, $net_wechat_unionid, $scan_log->shop_id, $net_user_id, $net_user_name, $net_scan_times + 1);
                }

                return $this->jsonReturn(200, '您是老用户,二维码可用，扫码成功');
            }
        }
        return $this->jsonReturn(400, '接口异常');
    }
}