<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrawRule extends Model
{
    static $typeDisplays = [
        'area' => '地区',
        'distributor' => '经销商',
        'shop' => '终端',
        'base' => '全局',
    ];

    protected $fillable = array('area_id', 'distributor_id', 'shop_id', 'rule_json');

    public function area() {
        return $this->belongsTo('App\Area', 'area_id');
    }

    public function distributor() {
        return $this->belongsTo('App\Distributor', 'distributor_id');
    }

    public function shop() {
        return $this->belongsTo('App\Shop', 'shop_id');
    }

    public function getRuleTypeAttribute() {
        if ($this->area_id > 0) {
            return 'area';
        } elseif ($this->distributor_id > 0) {
            return 'distributor';
        } elseif ($this->shop_id > 0) {
            return 'shop';
        } else {
            return 'base';
        }
    }

    public function getRuleTypeDisplayAttribute() {
        return DrawRule::$typeDisplays[$this->getRuleTypeAttribute()];
    }
}
