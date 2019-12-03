<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    protected $keepRevisionOf = ['salesman_id'];

    protected $fillable = ['name', 'level', 'distributor_id', 'area_id', 'address', 'salesman_id', 'owner_id', 'contact_phone', 'contact_person', 'category_id', 'per_consume', 'logo', 'source', 'net_shop_id'];

    public function getDeletedDisplayAttribute()
    {
        if (! $this->attributes['deleted_at']) {
            return '启用';
        }
        return '禁用';
    }

    public function scopeFilterStatus($query, $filter_status) {
        if (strlen($filter_status) > 0) {
            if ($filter_status == 1) {
                return $query->whereNull('deleted_at');
            } elseif ($filter_status == 2) {
                return $query->whereNotNull('deleted_at');
            }
        }
        return $query;
    }

    public function distributor()
    {
        return $this->belongsTo('App\Distributor', 'distributor_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'area_id');
    }

    public function shopActivity()
    {
        return $this->hasMany('App\ShopActivity', 'shop_id');
    }

    public function users()
    {
        return $this->hasMany('App\User', 'shop_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function salesman()
    {
        return $this->belongsTo('App\Salesman', 'salesman_id');
    }

    public function scopeKeyword($query, $keyword)
    {
        if (strlen($keyword) > 0) {
            return $query->where('name', 'LIKE', "%$keyword%");
        }
        return $query;
    }

    public function scopeLevel($query, $level)
    {
        if (in_array($level, ['A', 'B', 'C', 'D'])) {
            return $query->where('level', '=', $level);
        }
        return $query;
    }

    public function scopeDistributorName($query, $name)
    {
        if (strlen($name) == 0) {
            return $query;
        }

        $distributors = Distributor::where('name', 'LIKE', "%$name%")->get()->all();

        if (count($distributors) > 0) {
            return $query->whereIn('distributor_id', array_map(function ($n) {
                return $n->id;
            }, $distributors));
        }
        return $query->where('distributor_id', -1);
    }

    public function scopeSalesmanName($query, $name)
    {
        if (strlen($name) == 0) {
            return $query;
        }

        $salesmen = Salesman::where('name', 'LIKE', "%$name%")->get()->all();

        if (count($salesmen) > 0) {
            return $query->whereIn('salesman_id', array_map(function ($n) {
                return $n->id;
            }, $salesmen));
        }
        return $query->where('salesman_id', -1);
    }

    public function scopeAreaId($query, $areaId)
    {
        return $this->scopeArea($query, $areaId);
    }

    public function scopeArea($query, $areaId)
    {
        $areaId = intval($areaId);
        if ($areaId == 0) {
            return $query;
        }

        $areas = Area::where('id', '=', $areaId)
            ->orWhere('parent_id', '=', $areaId)
            ->orWhere('grandparent_id', '=', $areaId)
            ->get()->all();

        if (count($areas) > 0) {
            return $query->whereIn('area_id', array_map(function ($n) {
                return $n->id;
            }, $areas));
        }
        return $query;
    }

    public function scopeSalesman($query, $salesman_id)
    {
        if ($salesman_id) {
            return $query->whereIn('salesman_id', $salesman_id);
        }
        return $query;
    }

    public function scopeSalesmanId($query, $salesman_id)
    {
        if ($salesman_id) {
            return $query->where('salesman_id', $salesman_id);
        }
        return $query;
    }

    public function scopeShopId($query, $filter_shop_id)
    {
        if (strlen($filter_shop_id) > 0) {
            return $query->where('id', $filter_shop_id);
        }
        return $query;
    }
}
