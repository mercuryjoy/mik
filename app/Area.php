<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public function getTypeAttribute() {
        if ($this->attributes['grandparent_id'] != null) return "区县";
        if ($this->attributes['parent_id'] != null) return "市";
        return "省,直辖市";
    }

    public function parent_area() {
        return $this->belongsTo('App\Area', 'parent_id');
    }

    public function grandparent_area() {
        return $this->belongsTo('App\Area', 'grandparent_id');
    }
}
