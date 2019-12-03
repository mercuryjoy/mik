<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('net_api_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->foreign('id')->on('users');
            $table->integer('shop_id')->foreign('id')->on('shops');
            $table->integer('code_id')->foreign('id')->on('codes');
            $table->integer('api_id');
            $table->integer('net_user_id');
            $table->string('net_user_name', 20);
            $table->enum('role', ['user', 'net_user']);
            $table->enum('status', ['success', 'failed']);
            $table->string('comment', 255)->comment('日志内容');
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
        Schema::drop('net_api_logs');
    }
}
