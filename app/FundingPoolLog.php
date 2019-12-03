<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingPoolLog extends Model
{
    use SoftDeletes;

    private static $typeDisplayArray = [
        'deposit' => '存入资金',
        'user_withdraw' => '用户提现',
    ];

    protected $fillable = array('type', 'amount', 'balance', 'user_id', 'admin_id', 'comment');

    public function admin() {
        return $this->belongsTo('App\Admin', 'admin_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function getTypeDisplayAttribute() {
        return FundingPoolLog::$typeDisplayArray[$this->type] ?: $this->type;
    }

}
