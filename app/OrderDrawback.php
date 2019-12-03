<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDrawback extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'store_order_id', 'pay_money', 'drawback_money', 'pay_way', 'status', 'source'];

    public $statusArr = [
        'check' => '待审核',
        'finished' => '审核完成'
    ];

    public $payWayArr = [
        'balance' => '余额',
        'alipay' => '支付宝',
        'wechat' => '微信',
        'line' => '线下',
    ];

    public $sourceArr = [
        'cancel' => '取消订单',
    ];

    public function getStatusDisplayAttribute() {
        return $this->statusArr[$this->attributes['status']];
    }

    public function getSourceDisplayAttribute() {
        return $this->sourceArr[$this->attributes['source']];
    }

    public function getPayWayDisplayAttribute() {
        return $this->payWayArr[$this->attributes['pay_way']];
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function storeOrder()
    {
        return $this->belongsTo('App\StoreOrder', 'store_order_id');
    }

    public function scopeOrderId($query, $order_id)
    {
        if (strlen($order_id) > 0) {
            return $query->where('id', $order_id);
        }
        return $query;
    }
}
