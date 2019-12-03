<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserShopChangeLog extends Model
{
    protected $fillable = array('user_id', 'before_shop_id', 'after_shop_id');

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function before_shop() {
        return $this->belongsTo('App\Shop', 'before_shop_id');
    }

    public function after_shop() {
        return $this->belongsTo('App\Shop', 'after_shop_id');
    }
}
