<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMoneyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_money_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['adjustment', 'scan_prize', 'red_envelope', 'withdraw']);
            $table->integer('amount');
            $table->integer('user_id')->foreign('id')->on('users');
            $table->integer('admin_id')->foreign('id')->on('admins')->nullable();
            $table->integer('scan_log_id')->foreign('id')->on('scan_logs')->nullable();
            $table->text('comment');
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
        Schema::drop('user_money_logs');
    }
}
