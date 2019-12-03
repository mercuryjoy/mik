<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIsActivityToCodeBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_batches', function (Blueprint $table) {
            $table->dropColumn('is_activity');
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
            $table->enum('is_activity', ['yes', 'no'])->default('no')->comment('是否参加活动【no：否 yes：是】');
        });
    }
}
