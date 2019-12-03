<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pay extends Model
{
    use SoftDeletes;

    protected $fillable = ['pay_way', 'status', 'is_default', 'description'];

    public $payWayArr = [
        'balance' => '余额',
        'alipay' => '支付宝',
        'wechat' => '微信',
        'line' => '线下',
    ];

    public $statusArr = [
        '0' => '禁用',
        '1' => '启用',
    ];

    public $isDefaultArr = [
        '0' => '否',
        '1' => '是',
    ];


    public function getStatusDisplayAttribute()
    {
        return $this->statusArr[$this->attributes['status']];
    }

    public function getPayWayDisplayAttribute()
    {
        return $this->payWayArr[$this->attributes['pay_way']];
    }

    public function getIsDefaultDisplayAttribute()
    {
        return $this->isDefaultArr[$this->attributes['is_default']];
    }

    public function scopeStatus($query, $status)
    {
        if ($status !== '' && ($status == 0 || $status === 1)) {
            return $query->where('status', $status);
        }
        return $query;
    }



}
