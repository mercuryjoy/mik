<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPointLog extends Model
{
    private static $typeDisplayArray = [
        'adjustment' => '积分规则调整变动',
        'red_envelope' => '红包',
        'scan_prize' => '扫码奖励',
        'store_order_use' => '商城订单',
        'exchange_from_money' => '红包余额兑换',
        'user_scan_to_waiter' => '用户扫码发积分',
    ];

    protected $fillable = array('type', 'amount', 'user_id', 'admin_id', 'scan_log_id', 'store_order_id','comment', 'user_scan_log_id');

    public function admin() {
        return $this->belongsTo('App\Admin', 'admin_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function scanLog() {
        return $this->belongsTo('App\ScanLog', 'scan_log_id');
    }

    public function storeOrder() {
        return $this->belongsTo('App\StoreOrder', 'store_order_id');
    }

    public function getTypeDisplayAttribute() {
        return UserPointLog::$typeDisplayArray[$this->type] ?: $this->type;
    }
}
