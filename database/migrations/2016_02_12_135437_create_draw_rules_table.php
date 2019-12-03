<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draw_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('area_id')->foreign('id')->on('areas')->nullable()->unique();
            $table->integer('distributor_id')->foreign('id')->on('distributors')->nullable()->unique();
            $table->integer('shop_id')->foreign('id')->on('shops')->nullable()->unique();
            $table->string('rule_json', 1024);
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
        Schema::drop('draw_rules');
    }
}
