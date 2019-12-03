<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
	use SoftDeletes;

	protected $fillable = array('name', 'level', 'parent_distributor_id', 'area_id', 'address', 'contact', 'telephone');

    public function getDeletedDisplayAttribute()
    {
        if (! $this->attributes['deleted_at']) {
            return '启用';
        }
        return '禁用';
    }

	public function parent_distributor() {
		return $this->belongsTo('App\Distributor', 'parent_distributor_id');
	}

	public function child_distributors() {
		return $this->hasMany('App\Distributor', 'parent_distributor_id');
	}

	public function area() {
		return $this->belongsTo('App\Area', 'area_id');
	}

	public function scopeKeyword($query, $keyword) {
		if (strlen($keyword) > 0) {
			return $query->where('name', 'LIKE', "%$keyword%");
		}
		return $query;
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

	public function scopeLevel($query, $level) {
		$level = intval($level);
		if ($level === 1 || $level === 2) {
			return $query->where('level', '=', $level);
		}
		return $query;
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

		if (count($areas) > 0) {
			return $query->whereIn('area_id', array_map(function($n) {return $n->id;}, $areas));
		}
		return $query->where('area_id', -1);
	}
}
