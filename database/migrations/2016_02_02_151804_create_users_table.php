<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('telephone', 20)->unique();
            $table->string('password', 60)->nullable();
            $table->integer('shop_id')->foreign('id')->on('shops')->nullable();
            $table->integer('area_id')->foreign('id')->on('areas')->nullable();
            $table->enum('status', ['pending', 'normal'])->default('pending');
            $table->integer('money_balance')->default(0);
            $table->integer('point_balance')->default(0);
            $table->string('wechat_openid', 100)->nullable();
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
        Schema::drop('users');
    }
}
