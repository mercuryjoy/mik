<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesmen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->integer('salesman_id')->after('address')->foreign('id')->on('salesmen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('salesmen');

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('salesman_id');
        });
    }
}
