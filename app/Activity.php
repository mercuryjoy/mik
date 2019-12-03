<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table = 'activities';

    public $statuses = [
        'stop' => '禁用',
        'normal' => '启用',
    ];

    static $actionZoneDisplays = [
        'part' => '局部',
        'all' => '全局',
    ];

    static $typeDisplays = [
        'red_envelope' => '红包',
        'point' => '积分',
        'shop_owner' => '店长',
        'send_red_envelope' => '发红包'
    ];

    protected $fillable = ['title', 'rule_json', 'type', 'action_zone', 'status', 'start_at', 'end_at'];

    public function getStatusDisplayAttribute()
    {
        return $this->statuses[$this->attributes['status']];
    }

    public function getActionZoneDisplayAttribute()
    {
        return self::$actionZoneDisplays[$this->attributes['action_zone']];
    }

    public function getTypeDisplayAttribute()
    {
        return self::$typeDisplays[$this->attributes['type']];
    }

    public function getRuleJsonDisplayAttribute()
    {
        $type = $this->attributes['type'];
        $rule_json = json_decode($this->attributes['rule_json'], true);
        if (in_array($type, ['point', 'shop_owner'])) {
            $key = array_keys($rule_json[0])[0];
            $value = $rule_json[0][$key];
            return $value;
        }

        return '【无】';
    }
}
