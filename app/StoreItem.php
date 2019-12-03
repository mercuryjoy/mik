<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreItem extends Model
{
    use SoftDeletes;

    protected $fillable = array('name', 'price_money', 'price_point', 'is_virtual', 'description', 'stock', 'photo_url', 'status', 'type');

    public function getStatusDisplayAttribute() {
        return ['in_stock' => '正常', 'out_of_stock' => '下架', 'deleted' => '已删除'][$this->attributes['status']];
    }

    public function scopeStatus($query, $status) {
        if (in_array($status,['in_stock', 'out_of_stock', 'deleted'])) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }
}
