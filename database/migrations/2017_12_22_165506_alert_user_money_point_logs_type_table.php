<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertUserMoneyPointLogsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE user_money_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'withdraw', 'exchange_to_point', 'store_order_use', 'user_scan_to_waiter', 'waiter_scan_to_owner')");
        DB::statement("ALTER TABLE user_point_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'store_order_use', 'exchange_from_money', 'user_scan_to_waiter', 'waiter_scan_to_owner')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE user_money_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'withdraw', 'exchange_to_point', 'store_order_use')");
        DB::statement("ALTER TABLE user_point_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'store_order_use', 'exchange_from_money')");
    }
}
