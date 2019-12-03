<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salesman extends Model
{
    //软删除
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'status'];

    //关联shop表
    public function shops()
    {
        return $this->hasMany('App\Shop', 'salesman_id');
    }

    public $status_display = [
        '0' => '禁用',
        '1' => '启用'
    ];

    private $unKnow = '未知';

    public function getStatusDisplayAttribute()
    {
        if (! array_key_exists($this->attributes['status'], $this->status_display)) {
            return $this->unKnow;
        }

        return $this->status_display[$this->attributes['status']];
    }

    public function scopeFilterStatus($query, $filter_status)
    {
        if (strlen($filter_status) > 0 && array_key_exists($filter_status, $this->status_display)) {
            return $query->where('status', $filter_status);
        }

        return $query;
    }
    public function scopeFilterId($query, $id) {
        if (strlen($id) > 0) {
            return $query->where('id', '=', $id);
        }

        return $query;
    }
    public function scopeFilterName($query, $name) {
        if (strlen($name) > 0) {
            return $query->where('name', 'LIKE', "%$name%");
        }

        return $query;
    }
    public function scopeFilterPhone($query, $phone) {
        if (strlen($phone) > 0) {
            return $query->where('telephone', 'LIKE', "%$phone%");
        }

        return $query;
    }
}
