<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('seller_id', 50);
            $table->string('seller_email', 50);
            $table->string('buyer_id', 50);
            $table->string('buyer_logon_id', 50);
            $table->integer('buyer_pay_amount');
            $table->dateTime('gmt_payment');
            $table->dateTime('notify_time');
            $table->string('trade_no', 50);
            $table->string('trade_status', 20);
            $table->string('pay_way', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pay_logs');
    }
}
