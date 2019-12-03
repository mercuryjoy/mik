<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMoneyLog extends Model
{
    private static $typeDisplayArray = [
        'adjustment' => '金额规则调整变动',
        'scan_prize' => '扫码奖励',
        'red_envelope' => '红包',
        'withdraw' => '提现',
        'exchange_to_point' => '兑换积分',
        'store_order_use' => '商城订单',
        'user_scan_to_waiter' => '用户扫码奖励服务员',
        'waiter_scan_to_owner' => '服务员扫码奖励店长',
    ];

    protected $fillable = array('type', 'amount', 'user_id', 'admin_id', 'scan_log_id', 'comment', 'user_scan_log_id');

    public function admin() {
        return $this->belongsTo('App\Admin', 'admin_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function scanLog() {
        return $this->belongsTo('App\ScanLog', 'scan_log_id');
    }

    public function getTypeDisplayAttribute() {
        return UserMoneyLog::$typeDisplayArray[$this->type] ?: $this->type;
    }

}
