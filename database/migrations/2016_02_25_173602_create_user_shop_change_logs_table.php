<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserShopChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shop_change_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->foreign('id')->on('users');
            $table->integer('before_shop_id')->foreign('id')->on('shops');
            $table->integer('after_shop_id')->foreign('id')->on('shops');
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
        Schema::drop('user_shop_change_logs');
    }
}
