<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCodeBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_batches', function (Blueprint $table) {
            DB::statement("ALTER TABLE code_batches CHANGE `type` `type` ENUM('normal', 'activity', 'miniapp')");
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
            DB::statement("ALTER TABLE code_batches CHANGE `type` `type` ENUM('normal', 'activity')");
        });
    }
}
