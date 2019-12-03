<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopActivity extends Model
{
    public $timestamps = false;

    protected $table = 'shop_activity';

    protected $fillable = ['shop_id', 'activity_id'];

    public function shop()
    {
        return $this->belongsTo('App\Shop', 'shop_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Activity', 'activity_id');
    }
}
