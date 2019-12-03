<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('version', 20);
            $table->enum('type', ['android', 'ios', 'other']);
            $table->text('description', 255);
            $table->string('download_url', 255);
            $table->string('version_code', 255);
            $table->enum('is_force_update', ['yes', 'no']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_versions');
    }
}
