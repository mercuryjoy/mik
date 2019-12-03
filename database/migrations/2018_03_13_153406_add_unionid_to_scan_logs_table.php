<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnionidToScanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->string('net_wechat_unionid', 100)->nullable();
            $table->integer('distributor_id')->foreign('id')->on('distributors');
            $table->integer('salesman_id')->foreign('id')->on('salesmen');
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
            $table->dropColumn(['net_wechat_unionid', 'distributor_id', 'salesman_id']);
        });
    }
}
