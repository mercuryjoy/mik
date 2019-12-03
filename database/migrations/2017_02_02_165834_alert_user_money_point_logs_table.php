<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertUserMoneyPointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE user_money_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'withdraw', 'exchange_to_point', 'store_order_use')");
        DB::statement("ALTER TABLE user_point_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'store_order_use', 'exchange_from_money')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE user_money_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'red_envelope', 'withdraw')");
        DB::statement("ALTER TABLE user_point_logs CHANGE `type` `type` ENUM('adjustment', 'scan_prize', 'store_order_use')");
    }
}
