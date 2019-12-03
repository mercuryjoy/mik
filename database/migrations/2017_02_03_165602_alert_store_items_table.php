<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertStoreItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('store_items', function (Blueprint $table) {
            $table->renameColumn('price', 'price_point');
            $table->integer('price_money')->after('description')->default(0);
            $table->boolean('is_virtual')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('store_items', function (Blueprint $table) {
            $table->renameColumn('price_point', 'price');
            $table->dropColumn('price_money');
            $table->dropColumn('is_virtual');
        });
    }
}
