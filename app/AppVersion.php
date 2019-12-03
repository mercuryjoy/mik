<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Model
{
    use SoftDeletes;

    static $isForceUpdate = [
        'yes' => '是',
        'no' => '否',
    ];

    static $type = [
        'ios' => 'Ios',
        'android' => 'Android',
        'other' => '其他',
    ];

    protected $table = 'app_versions';

    protected $primaryKey = 'id';

    protected $fillable = ['version', 'type', 'description', 'is_force_update', 'download_url', 'version_code'];

    public function getIsForceUpdateDisplayAttribute()
    {
        return self::$isForceUpdate[$this->attributes['is_force_update']];
    }

    public function getTypeDisplayAttribute()
    {
        return self::$type[$this->attributes['type']];
    }

    public function scopeType($query, $type)
    {
        if (strlen(trim($type)) > 0) {
            return $query->where('type', $type);
        }
        return $query;
    }

    public function scopeIsForceUpdate($query, $is_force_type)
    {
        if (strlen(trim($is_force_type)) > 0) {
            return $query->where('is_force_update', $is_force_type);
        }
        return $query;
    }
}
