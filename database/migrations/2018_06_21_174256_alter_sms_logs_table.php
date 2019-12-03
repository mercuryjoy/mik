<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE sms_logs CHANGE `type` `type` ENUM('verify_register', 'verify_register_password', 'verify_reset_password', 'wechat_bind', 'wechat_unbind', 'withdraw', 'update_login_password', 'update_withdraw_password', 'pass_audit', 'admin_notify_daily_cost', 'admin_notify_scan_count',
                        'admin_notify_funding_pool', 'test', 'other')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE sms_logs CHANGE `type` `type` ENUM('verify_register', 'pass_audit', 'admin_notify_daily_cost', 'admin_notify_scan_count', 'admin_notify_funding_pool', 'test', 'others')");
    }
}
