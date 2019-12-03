<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    protected $fillable = array('title', 'thumbnail_url', 'content_url','order_id');

    public function scopeFilterName($query, $filter_name)
    {
    	if (isset($filter_name)) {
    		return $query->where('title', 'like', '%' . $filter_name .'%');
    	}
    	return $query;
    }
}
