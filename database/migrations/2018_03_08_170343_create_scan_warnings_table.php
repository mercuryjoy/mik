<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScanWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->forgine('id')->on('users');
            $table->integer('shop_id')->forgine('id')->on('shops');
            $table->integer('net_user_id');
            $table->string('net_user_name', 20);
            $table->integer('times');
            $table->tinyInteger('warning_type')->comment('0:未知，1：天，2：周，3：月，4：连续');
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
        Schema::drop('scan_warnings');
    }
}
