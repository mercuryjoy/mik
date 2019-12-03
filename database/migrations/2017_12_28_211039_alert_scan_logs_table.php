<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertScanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropColumn('coupon_id');
            $table->string('coupon_code', 20);
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
            $table->integer('coupon_id')->comment('券码ID：NET');
            $table->dropColumn('coupon_code');
        });
    }
}
