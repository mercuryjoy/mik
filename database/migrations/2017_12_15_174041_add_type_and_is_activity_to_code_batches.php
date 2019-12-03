<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndIsActivityToCodeBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_batches', function (Blueprint $table) {
            $table->enum('type', ['normal', 'activity'])->default('normal')->comment('二维码类型【normal：普通二维码 activity：活动二维码】');
            $table->enum('is_activity', ['yes', 'no'])->default('no')->comment('是否参加活动【no：否 yes：是】');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('code_batches', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_activity']);
        });
    }
}
