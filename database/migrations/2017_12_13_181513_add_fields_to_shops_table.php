<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('logo', 255)->comment('LOGO');
            $table->integer('owner_id')->foreign('id')->on('users')->comment('店长ID');
            $table->string('contact_person', 20)->comment('联系人');
            $table->string('contact_phone', 13)->comment('联系手机号');
            $table->integer('category_id')->foreign('id')->on('categories')->comment('餐饮类别ID');
            $table->decimal('per_consume', 5, 2)->comment('人均消费');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['logo', 'owner_id', 'contact_person', 'contact_phone', 'category_id', 'per_consume']);
        });
    }
}
