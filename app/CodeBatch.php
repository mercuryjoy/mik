<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeBatch extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'count', 'status', 'type',];

    public $type_display = [
        'normal' => '普通',
        'activity' => '活动',
        'miniapp' => '小程序专用'
    ];

    public $status_display = [
        'normal' => '正常',
        'frozen' => '冻结'
    ];

    public function getStatusDisplayAttribute()
    {
        return $this->status_display[$this->attributes['status']];
    }

    public function getTypeDisplayAttribute()
    {
        return $this->type_display[$this->attributes['type']];
    }

    public function code()
    {
        return $this->hasMany('App\Code', 'batch_id');
    }
}
