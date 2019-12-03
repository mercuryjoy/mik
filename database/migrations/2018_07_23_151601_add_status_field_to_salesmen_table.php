<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldToSalesmenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salesmen', function (Blueprint $table) {
            $table->boolean('status')->default(false)->comment('状态：[0:禁用，1:启用]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salesmen', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
