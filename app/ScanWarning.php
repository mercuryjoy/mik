<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScanWarning extends Model
{
    protected $table = 'scan_warnings';

    protected $fillable = ['user_id', 'net_user_id', 'net_user_name', 'shop_id', 'times', 'warning_type'];

    protected $warningTypeArr = [
        0 => '未知',
        1 => '每天核销三次及以上',
        2 => '近7天核销3次及以上',
        3 => '近30天核销5次及以上',
        4 => '连续两天核销',
        5 => '服务员作弊预警',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function getWarningTypeDisplayAttribute()
    {
        return $this->warningTypeArr[$this->attributes['warning_type']];
    }

    public function scopeUserId($query, $user_id)
    {
        if (strlen($user_id) > 0) {
            return $query->where('user_id', $user_id);
        }
        return $query;
    }

    public function scopeUserName($query, $user_name)
    {
        if (strlen($user_name) > 0) {
            $user_ids = User::where('name', 'like', '%'.$user_name.'%')->pluck('id');
            if (count($user_ids) > 0) {
                return $query->whereIn('user_id', $user_ids);
            }
            return $query->where('user_id', 0);
        }
        return $query;
    }

    public function scopeShopId($query, $shop_id)
    {
        if (strlen($shop_id) > 0) {
            return $query->where('shop_id', $shop_id);
        }
        return $query;
    }

    public function scopeShopName($query, $shop_name)
    {
        if (strlen($shop_name) > 0) {
            $shop_ids = Shop::where('name', 'like', '%'.$shop_name.'%')->pluck('id');
            if (count($shop_ids) > 0) {
                return $query->whereIn('shop_id', $shop_ids);
            }
            return $query->where('shop_id', 0);
        }
        return $query;
    }

    public function scopeNetUserId($query, $net_user_id)
    {
        if (strlen($net_user_id) > 0) {
            return $query->where('net_user_id', $net_user_id);
        }
        return $query;
    }

    public function scopeNetUserName($query, $net_user_name)
    {
        if (strlen($net_user_name) > 0) {
            return $query->where('net_user_name', 'like', $net_user_name);
        }
        return $query;
    }

    public function scopeWarningType($query, $warning_type)
    {
        if (strlen($warning_type) > 0 && in_array($warning_type, [0,1,2,3,4])) {
            switch ($warning_type) {
                case 'over_three_one_day':
                    $trans_warning_type = 1;
                    break;
                case 'over_three_one_weekend':
                    $trans_warning_type = 2;
                    break;
                case 'over_five_one_month':
                    $trans_warning_type = 3;
                    break;
                case 'keep_two_day':
                    $trans_warning_type = 4;
                    break;
                case 'same_user_scan':
                    $trans_warning_type = 5;
                    break;
                default:
                    $trans_warning_type = 0;
                    break;
            }

            return $query->where('warning_type', $trans_warning_type);
        }
        return $query;
    }
}
