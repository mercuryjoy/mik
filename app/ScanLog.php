<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScanLog extends Model
{
    private $type = [
        'waiter' => '服务员收益',
        'owner' => '店长收益',
        'unknown' => '[未知]'
    ];

    private $userType = [
        'new' => '新用户',
        'old' => '老用户',
        'net_no_scan' => '用户未扫码',
        'user_no_scan' => '服务员未扫码',
        'unknown' => '[未知]'
    ];

    use SoftDeletes;

    protected $fillable = array('code_id', 'user_id', 'shop_id', 'luck_id', 'money', 'point', 'waiter_id', 'type', 'net_user_id', 'net_user_name', 'net_user_times', 'coupon_code', 'coupon_name', 'net_wechat_unionid', 'salesman_id', 'distributor_id');

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function waiter() {
        return $this->belongsTo('App\User', 'waiter_id');
    }

    public function code() {
        return $this->belongsTo('App\Code', 'code_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function shop() {
        return $this->belongsTo('App\Shop', 'shop_id');
    }

    public function salesman() {
        return $this->belongsTo('App\Salesman', 'salesman_id');
    }

    public function scopeUserName($query, $userName) {
        if (strlen($userName) == 0) {
            return $query;
        }

        $users = User::withTrashed()->where('name', 'LIKE', "%$userName%")->get()->all();
        if (count($users) > 0) {
            return $query->whereIn('user_id', array_map(function($n) {return $n->id;}, $users));
        }
        return $query->where('user_id', -1);
    }

    public function scopeUserId($query, $user_id) {
        if (strlen($user_id) > 0) {
            return $query->where('user_id', $user_id);
        }

        return $query;
    }

    public function scopeShopId($query, $shop_id) {
        if (strlen($shop_id) > 0) {
            return $query->where('shop_id', $shop_id);
        }

        return $query;
    }

    public function scopeShopName($query, $shopName) {
        if (strlen($shopName) == 0) {
            return $query;
        }

        $shops = Shop::withTrashed()->where('name', 'LIKE', "%$shopName%")->get()->all();
        if (count($shops) > 0) {
            return $query->whereIn('shop_id', array_map(function($n) {return $n->id;}, $shops));
        }
        return $query->where('shop_id', -1);
    }

    public function scopeDistributorName($query, $distributorName) {
        if (strlen($distributorName) == 0) {
            return $query;
        }

        $shops = Shop::distributorName($distributorName)->get()->all();

        if (count($shops) > 0) {
            return $query->whereIn('shop_id', array_map(function($n) {return $n->id;}, $shops));
        }
        return $query->where('shop_id', -1);
    }

    public function scopeSalesmanName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $shops = Shop::salesmanName($name)->get()->all();

        if (count($shops) > 0) {
            return $query->whereIn('shop_id', array_map(function($n) {return $n->id;}, $shops));
        }
        return $query->where('shop_id', -1);
    }

    // query salesman
    public function scopeFilterSalesmanName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $salesmen = Salesman::where('name', 'like', '%'.$name.'%')->pluck('id');

        if ($salesmen->count() > 0) {
            return $query->whereIn('salesman_id', $salesmen);
        }

        return $query->where('salesman_id', -1);
    }

    public function scopeArea($query, $areaId) {
        $areaId = intval($areaId);
        if ($areaId == 0) {
            return $query;
        }

        $shops = Shop::areaId($areaId)->get()->all();
        if (count($shops) > 0) {
            return $query->whereIn('shop_id', array_map(function($n) {return $n->id;}, $shops));
        }
        return $query->where('shop_id', -1);
    }

    public function scopeCode($query, $code) {
        if (strlen($code) == 0) {
            return $query;
        }

        $code = Code::where('code', $code)->first();
        if ($code != null) {
            return $query->where('code_id', $code->id);
        }

        return $query->where('code_id', -1);
    }

    public function scopeScanUserName($query, $scanUserName) {
        if (strlen($scanUserName) == 0) {
            return $query;
        }

        return $query->where('net_user_name', 'LIKE', "%$scanUserName%");
    }

    public function scopeNetUserId($query, $net_user_id) {
        if (strlen($net_user_id) > 0) {
            return $query->where('net_user_id', $net_user_id);
        }

        return $query;
    }

    public function scopeSalesmanId($query, $salesman_id) {
        if (strlen($salesman_id) > 0) {
            return $query->where('salesman_id', $salesman_id);
        }

        return $query;
    }

    public function scopeScanType($query, $scanType) {
        if ($scanType) {
            if ($scanType == 'waiter_user_scan_over') {
                return $query->where('user_id', '>', 0)->where('net_user_id', '>', 0)->where('waiter_id', 0);
            } elseif ($scanType == 'waiter_owner_scan_over') {
                return $query->where('user_id', '>', 0)->where('net_user_id', 0)->where('waiter_id', '>', 0);
            } elseif ($scanType == 'waiter_scan_user_no') {
                return $query->where('user_id', '>', 0)->where('net_user_id', 0)->where('waiter_id', 0);
            } elseif ($scanType == 'user_scan_waiter_no') {
                return $query->where('user_id', 0)->where('net_user_id', '>', 0)->where('waiter_id', 0);
            } else {
                return $query;
            }
        }

        return $query;
    }

    public function scopeUserType($query, $scanType) {
        if ($scanType) {
            if ($scanType == 'new_net_user') {
                return $query->where('net_user_times', 1)->where('user_id', '>', 0)->where('net_user_id', '>', 0)->where('waiter_id', 0);
            } elseif ($scanType == 'old_net_user') {
                return $query->where('net_user_times', '>', 1)->where('user_id', '>', 0)->where('net_user_id', '>', 0)->where('waiter_id', 0);
            } elseif ($scanType == 'net_no_scan') {
                return $query->where('user_id', '>', 0)->where('net_user_id', 0)->where('waiter_id', 0);
            } elseif ($scanType == 'user_no_scan') {
                return $query->where('user_id', 0)->where('net_user_id', '>', 0)->where('waiter_id', 0);
            } else {
                return $query;
            }
        }

        return $query;
    }


    public function scopeType($query, $type)
    {
        if ($type) {
            if ($type == 'waiter') {
                return $query->where('net_user_id', '!=', 0);
            } elseif ($type == 'owner') {
                return $query->where('waiter_id', '!=', 0);
            } else {
                return $query;
            }
        }

        return $query;
    }

    public function scopeSalesmanIds($query, $salesman_id)
    {
        if ($salesman_id) {
            return $query->whereIn('salesman_id', $salesman_id);
        }
        return $query;
    }

    public function getTypeValueAttribute()
    {
        if (($this->attributes['net_user_id'] > 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] > 0) ||
            ($this->attributes['net_user_id'] == 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] > 0) ||
            ($this->attributes['net_user_id'] > 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] == 0)) {
            return 'waiter';
        } elseif ($this->attributes['net_user_id'] == 0 && $this->attributes['waiter_id'] > 0 && $this->attributes['user_id'] > 0) {
            return 'owner';
        }
        return 'unKnown';
    }

    public function getTypeDisplayAttribute()
    {
        if ($this->getTypeValueAttribute() !== 'unKnown') {
            return $this->type[$this->getTypeValueAttribute()];
        }
        return '未知';
    }

    public function getUserTypeAttribute()
    {
        $net_user_times = $this->attributes['net_user_times'];
        $user_id = $this->attributes['user_id'];
        $net_user_id = $this->attributes['net_user_id'];
        $waiter_id = $this->attributes['waiter_id'];

        if ($net_user_times == 1 && $user_id > 0 && $net_user_id > 0 && $waiter_id == 0) {
            return 'new';
        } elseif ($net_user_times > 1 && $user_id > 0 && $net_user_id > 0 && $waiter_id == 0) {
            return 'old';
        } elseif ($user_id > 0 && $net_user_id == 0 && $waiter_id == 0 && $net_user_times == 0) {
            return 'net_no_scan';
        } elseif ($user_id == 0 && $net_user_id > 0 && $waiter_id == 0) {
            return 'user_no_scan';
        }
        return 'unKnown';
    }

    public function getUserTypeDisplayAttribute()
    {
        if ($this->getUserTypeAttribute() !== 'unKnown') {
            return $this->userType[$this->getUserTypeAttribute()];
        }
        return '未知';
    }

    public function getTypeDetailAttribute()
    {
        if ($this->attributes['net_user_id'] > 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] > 0) {
            return 'waiter_user_scan_over';
        } elseif ($this->attributes['net_user_id'] == 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] > 0) {
            return 'waiter_scan_user_no';
        } elseif ($this->attributes['net_user_id'] > 0 && $this->attributes['waiter_id'] == 0 && $this->attributes['user_id'] == 0) {
            return 'user_scan_waiter_no';
        } elseif ($this->attributes['net_user_id'] == 0 && $this->attributes['waiter_id'] > 0 && $this->attributes['user_id'] > 0) {
            return 'waiter_owner_scan_over';
        } else {
            return 'unKnown';
        }
    }
}
