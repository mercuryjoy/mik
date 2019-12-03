<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code_id')->foreign('id')->on('codes');
            $table->integer('user_id')->foreign('id')->on('users');
            $table->integer('shop_id')->foreign('id')->on('shops')->nullable();
            $table->string('luck_id', 10);
            $table->integer('money');
            $table->integer('point');
            $table->softDeletes();
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
        Schema::drop('scan_logs');
    }
}
