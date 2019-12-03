<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetApiLog extends Model
{
    use SoftDeletes;

    protected $table = "net_api_logs";

    protected $fillable = array('shop_id', 'user_id',  'api_id', 'role', 'net_user_id', 'net_user_name', 'status', 'comment');
}
