<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDrawbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_drawbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('store_order_id');
            $table->integer('pay_money');
            $table->integer('drawback_money');
            $table->enum('pay_way', ['balance', 'alipay', 'wechat', 'line'])->nullable();
            $table->enum('status', ['check', 'finished'])->default('check');
            $table->enum('source', ['cancel']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_drawbacks');
    }
}
