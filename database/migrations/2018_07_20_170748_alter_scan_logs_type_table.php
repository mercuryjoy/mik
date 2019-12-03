<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterScanLogsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            DB::statement("ALTER TABLE scan_logs CHANGE `type` `type` ENUM('scan_prize', 'scan_coupon', 'scan_send_money_activity', 'miniapp_user_scan')");
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
            DB::statement("ALTER TABLE scan_logs CHANGE `type` `type` ENUM('scan_prize', 'scan_coupon', 'scan_send_money_activity')");
        });
    }
}
