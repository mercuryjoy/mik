<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 1024);
            $table->integer('price')->default(0);
            $table->integer('stock')->default(0);
            $table->string('photo_url', 1024);
            $table->enum('status', ['in_stock', 'out_of_stock', 'deleted'])->default('in_stock');
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
        Schema::drop('store_items');
    }
}
