<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    protected $table = "sms_logs";
    protected $fillable = array('telephone', 'content', 'type', 'status', 'code', 'comment');

    public function getTypeDisplayAttribute() {
        $typeDisplays = [
            'verify_register' => '注册验证',
            'verify_register_password' => '注册效验码',
            'verify_reset_password' => '密码重置效验码',
            'wechat_bind' => '微信绑定效验码',
            'wechat_unbind' => '微信解绑效验码',
            'withdraw' => '提现效验码',
            'update_login_password' => '修改登录密码效验码',
            'update_withdraw_password' => '修改提现密码效验码',
            'pass_audit' => '用户审核',
            'admin_notify' => '智能提醒',
            'admin_notify_daily_cost' => '智能提醒[每日奖金支出警示]',
            'admin_notify_scan_count' => '智能提醒[同一用户一天内扫码数量上限]',
            'admin_notify_funding_pool' => '智能提醒[资金池余额警示额度]',
            'test' => '测试',
            'others' => '其他',
        ];


        
        $type = $this->attributes['type'];
        return array_key_exists($type, $typeDisplays) ? $typeDisplays[$type] : $type;
    }

    public function getStatusDisplayAttribute() {
        return [
            'sent' => '已发送',
            'error' => '发送失败',
            'used' => '已使用',
        ][$this->attributes['status']];
    }

}
