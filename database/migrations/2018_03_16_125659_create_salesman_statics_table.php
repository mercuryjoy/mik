<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmanStaticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesman_statics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salesman_id');
            $table->integer('user_count');
            $table->integer('total_user_count');
            $table->integer('shop_count');
            $table->integer('total_shop_count');
            $table->integer('scan_count');
            $table->integer('scan_money');
            $table->integer('sales_money');
            $table->integer('scan_shop_count');
            $table->integer('no_scan_shop_count');
            $table->integer('scan_user_count');
            $table->integer('scan_count_percent');
            $table->integer('shop_scan_percent');
            $table->date('statics_date');
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
        Schema::drop('salesman_statics');
    }
}
