<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopActivitiyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->foreign('id')->on('shops');
            $table->integer('activity_id')->foreign('id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shop_activity');
    }
}
