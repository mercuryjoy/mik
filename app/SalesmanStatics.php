<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesmanStatics extends Model
{
    protected $table = 'salesman_statics';

    protected $primaryKey = 'id';

    protected $fillable = [
        'salesman_id', 'user_count', 'shop_count', 'scan_count', 'scan_money', 'sales_money', 'statics_date', 'total_user_count', 'total_shop_count'
    ];
}
