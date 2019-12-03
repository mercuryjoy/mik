<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToScanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->enum('type', ['scan_prize', 'scan_coupon', 'scan_send_money_activity'])->comment('扫码类型【空：普通二维码 scan_coupon：券码核销 scan_send_money_activity：扫描发红包活动】');
            $table->integer('waiter_id')->foreign('id')->on('users')->comment('服务员ID：type为scan_send_money_activity，并且net_user_id为0时代表服务员扫码给店长发红包');
            $table->integer('net_user_id')->comment('普通用户ID：NET');
            $table->string('net_user_name', 20)->comment('普通用户名称：NET');
            $table->integer('net_user_times')->comment('普通用户扫码次数：NET');
            $table->integer('coupon_id')->comment('券码ID：NET');
            $table->string('coupon_name', 30)->comment('券码名称：NET');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropColumn(['type', 'waiter_id', 'net_user_id', 'net_user_name', 'net_user_times', 'coupon_id', 'coupon_name']);
        });
    }
}
