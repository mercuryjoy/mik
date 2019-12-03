<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayLog extends Model
{
    protected $fillable = ['order_id', 'seller_id', 'seller_email', 'buyer_id', 'buyer_logon_id', 'buyer_pay_amount', 'gmt_payment', 'notify_time', 'trade_no', 'trade_status', 'pay_way'];
}
