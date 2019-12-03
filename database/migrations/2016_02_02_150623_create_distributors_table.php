<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('level')->default(1);
            $table->integer('parent_distributor_id')->nullable()->foreign('id')->on('distributors')->onDelete('cascade');;
            $table->integer('area_id')->foreign('id')->on('areas');
            $table->string('address', 200)->default('');
            $table->string('contact', 20)->default('');
            $table->string('telephone', 20)->default('');
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
        Schema::drop('distributors');
    }
}
