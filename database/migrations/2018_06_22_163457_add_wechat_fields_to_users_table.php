<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWechatFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wechat_name')->nullable()->comment('微信昵称');
            $table->string('wechat_avatar')->nullable()->comment('微信头像');
            $table->string('pay_password')->nullable()->comment('支付密码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wechat_name', 'wechat_avatar', 'pay_password']);
        });
    }
}
