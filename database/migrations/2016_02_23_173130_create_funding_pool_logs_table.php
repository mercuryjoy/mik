<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundingPoolLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_pool_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['deposit', 'user_withdraw']);
            $table->integer('amount');
            $table->integer('balance');
            $table->integer('user_id')->foreign('id')->on('users')->nullable();
            $table->integer('admin_id')->foreign('id')->on('admins')->nullable();
            $table->text('comment');
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
        Schema::drop('funding_pool_logs');
    }
}
