<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use SoftDeletes;

    protected $fillable = array('name', 'level', 'parent_id');

    public function scopeKeyword($query, $keyword)
    {
        if (strlen(trim($keyword)) > 0) {
            return $query->where('name', 'like', '%'.$keyword.'%');
        }
        return $query;
    }

    public function scopeLevel($query, $level)
    {
        $level = intval($level);
        if ($level) {
            return $query->where('level', $level);
        }
        return $query;
    }

    public function parentCategory()
    {
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function childCategory() {
        return $this->hasMany('App\Category', 'parent_id');
    }
}
