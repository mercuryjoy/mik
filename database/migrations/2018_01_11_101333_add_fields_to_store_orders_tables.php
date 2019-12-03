<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToStoreOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_orders', function (Blueprint $table) {
            $table->string('contact_name', 30)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->integer('money');
            $table->integer('distributor_id')->foreign('id')->on('distributors');
            $table->integer('salesman_id')->foreign('id')->on('salesmen');
            $table->integer('shop_id')->foreign('id')->on('shops');
            $table->boolean('is_pay')->default(false);
            $table->enum('pay_way', ['balance', 'alipay', 'wechat', 'line'])->nullable();
            $table->boolean('is_checked')->default(false);
            DB::statement("ALTER TABLE store_orders CHANGE `status` `status` ENUM('established', 'created', 'shipped', 'canceled', 'finished')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_orders', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'money', 'salesman_id', 'distributor_id', 'shop_id', 'is_checked', 'pay_way', 'is_pay']);
            DB::statement("ALTER TABLE store_orders CHANGE `status` `status` ENUM('created', 'shipped', 'canceled')");
        });
    }
}
