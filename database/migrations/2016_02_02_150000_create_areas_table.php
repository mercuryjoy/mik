<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 20);
            $table->integer('parent_id')->nullable()->foreign('id')->on('areas')->onDelete('cascade');
            $table->integer('grandparent_id')->nullable()->foreign('id')->on('areas')->onDelete('cascade');
            $table->string('display', 30);
            $table->softDeletes();
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
        Schema::drop('areas');
    }
}
