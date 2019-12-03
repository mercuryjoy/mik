<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UserTraits;

/**
 */
class User extends Model
{
    use SoftDeletes, UserTraits;
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    protected $keepRevisionOf = ['shop_id'];

    protected $fillable = array('name', 'gender', 'telephone', 'password', 'shop_id', 'status', 'wechat_openid', 'wechat_unionid', 'wechat_name', 'wechat_avatar', 'pay_password');
    protected $hidden = ['password', 'pay_password'];

    public function getGenderDisplayAttribute() {
        $gender = $this->attributes['gender'];
        if (strlen($gender) == 0) return '';
        return ['male' => '男', 'female' => '女'][$gender];
    }

    public function scopeFilterDeleteStatus($query, $filter_delete_status) {
        if (strlen($filter_delete_status) > 0) {
            if ($filter_delete_status == 1) {
                return $query->whereNull('deleted_at');
            } elseif ($filter_delete_status == 2) {
                return $query->whereNotNull('deleted_at');
            }
        }
        return $query;
    }

    public function getStatusDisplayAttribute() {
        return ['normal' => '正常', 'pending' => '待审核'][$this->attributes['status']];
    }

    public function getDeletedDisplayAttribute()
    {
        if (! $this->attributes['deleted_at']) {
            return '启用';
        }
        return '禁用';
    }

    public function shop() {
        return $this->belongsTo('App\Shop', 'shop_id');
    }

    public function storeOrder() {
        return $this->hasMany('App\StoreOrder', 'user_id');
    }

    public function scanLog() {
        return $this->hasMany('App\ScanLog', 'user_id');
    }

    public function userScanGetPointLog() {
        return $this->hasMany('App\ScanLog', 'user_id');
    }

    public function scopeArea($query, $areaId) {
        $areaId = intval($areaId);
        if ($areaId == 0) {
            return $query;
        }

        $areas = Area::where('id', '=', $areaId)
            ->orWhere('parent_id', '=', $areaId)
            ->orWhere('grandparent_id', '=', $areaId)
            ->get()->all();

        $shops = Shop::whereIn('area_id', array_map(function($n) {return $n->id;}, $areas))->get()->all();
        return $query->whereIn('shop_id', array_map(function($n) {return $n->id;}, $shops));
    }

    public function scopeStatus($query, $status) {
        if (in_array($status, ['pending', 'normal'])) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }

    public function scopePhone($query, $phone) {
        if (strlen($phone) > 0) {
            return $query->where('telephone', 'LIKE', "%$phone%");
        }
        return $query;
    }

    public function scopeShopName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $shops = Shop::where('name', 'LIKE', "%$name%")->get()->all();

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

    public function scopeUserName($query, $name) {
        if (strlen($name) > 0) {
            return $query->where('name', 'LIKE', "%$name%");
        }

        return $query;
    }
}
