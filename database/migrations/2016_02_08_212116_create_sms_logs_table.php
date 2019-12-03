<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('telephone', 20)->index();
            $table->string('content', 100);
            $table->enum('type', ['verify_register', 'pass_audit', 'admin_notify_daily_cost', 'admin_notify_scan_count', 'admin_notify_funding_pool', 'test', 'others']);
            $table->enum('status', ['sent', 'error', 'used']);
            $table->string('code', 10)->nullable();
            $table->string('comment', 100)->nullable();
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
        Schema::drop('sms_logs');
    }
}
