<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreOrder extends Model
{
    use SoftDeletes;
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    protected $keepRevisionOf = ['status', 'is_checked'];

    protected $fillable = array('item_id', 'user_id', 'amount', 'status', 'shipping_address', 'remarks', 'type', 'is_checked', 'pay_way', 'distributor_id', 'salesman_id', 'shop_id', 'is_pay', 'contact_name', 'contact_phone', 'money');

    public $statusArr = [
        'established' => '订单成立',
        'created' => '待发货',
        'shipped' => '已发货',
	    'drawback' => '取消订单待审核',
        'canceled' => '已取消',
        'finished' => '已完成'
    ];

    public $payWayArr = [
        'balance' => '余额',
        'alipay' => '支付宝',
        'wechat' => '微信',
        'line' => '线下',
    ];

    public $isPayArr = [
        '0' => '未支付',
        '1' => '已支付'
    ];

    public $isCheckedArr = [
        '0' => '未审核',
        '1' => '已审核'
    ];

    public function getStatusDisplayAttribute() {
        return $this->statusArr[$this->attributes['status']];
    }

    public function getCheckedDisplayAttribute() {
        return $this->checkedArr[$this->attributes['is_checked']];
    }

    public function getPayWayDisplayAttribute() {
        if ($this->attributes['pay_way']) {
            return $this->payWayArr[$this->attributes['pay_way']];
        }
        return '[暂未付款]';
    }

    public function getIsPayDisplayAttribute() {
        return $this->isPayArr[$this->attributes['is_pay']];
    }

    public function getIsCheckedDisplayAttribute() {
        return $this->isCheckedArr[$this->attributes['is_checked']];
    }

    public function scopeUserName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $users = User::where('name', 'LIKE', "%$name%")->get()->all();

        if (count($users) > 0) {
            return $query->whereIn('user_id', array_map(function($n) {return $n->id;}, $users));
        }
        return $query->where('user_id', -1);
    }

    public function scopeShopName($query, $name)
    {
        if (strlen($name) == 0) {
            return $query;
        }

        $shops = Shop::where('name', 'LIKE', "%$name%")->pluck('id');

        if (count($shops) > 0) {
            return $query->whereIn('shop_id', $shops);
        }
        return $query->where('shop_id', -1);
    }

    public function scopeSalesmanName($query, $name)
    {
        if (strlen($name) == 0) {
            return $query;
        }

        $salesmen = Salesman::where('name', 'LIKE', "%$name%")->pluck('id');

        if (count($salesmen) > 0) {
            return $query->whereIn('salesman_id', $salesmen);
        }
        return $query->where('salesman_id', -1);
    }

    public function scopeItemName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $items = StoreItem::where('name', 'LIKE', "%$name%")->get()->all();

        if (count($items) > 0) {
            return $query->whereIn('item_id', array_map(function($n) {return $n->id;}, $items));
        }
        return $query->where('item_id', -1);
    }

    public function scopeStatus($query, $status) {
        if (in_array($status,['created', 'established', 'shipped', 'canceled', 'drawback', 'finished'])) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }

    public function scopeChecked($query, $checked) {
        if (in_array($checked, ['false', 'true'])) {
            if ($checked == 'true') {
                return $query->where('is_checked', 1);
            } elseif ($checked == 'false') {
                return $query->where('is_checked', 0);
            }
        }
        return $query;
    }

    public function scopeItemId($query, $itemId)
    {
        if ($itemId) {
            return $query->where('item_id', $itemId);
        }
        return $query;
    }

    public function scopeSalesman($query, $salesman_id) {
        if (!empty($salesman_id)) {
            return $query->where('salesman_id', $salesman_id);
        }
        return $query;
    }

    public function scopeShop($query, $shop_id) {
        if (!empty($shop_id)) {
            return $query->where('shop_id', $shop_id);
        }
        return $query;
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function shop() {
        return $this->belongsTo('App\Shop', 'shop_id');
    }

    public function item() {
        return $this->belongsTo('App\StoreItem', 'item_id');
    }

    public function drawback()
    {
	    return $this->hasOne('App\OrderDrawback', 'store_order_id');
    }

    public function salesman()
    {
        return $this->belongsTo('App\Salesman');
    }

    public function UserPointLog()
    {
        return $this->hasOne(UserPointLog::class, 'store_order_id', 'id');
    }
}
