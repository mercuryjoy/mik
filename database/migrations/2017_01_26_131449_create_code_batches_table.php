<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodeBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->integer('count');
            $table->enum('status', ['normal', 'frozen']);
            $table->softDeletes();
            $table->timestamps();
        });

        // Gather all batches from existing 'codes' table
        $existingBatches = DB::table('codes')
            ->select('batch as name', DB::raw('count(*) as count'), 'created_at', 'updated_at')
            ->groupBy('batch')
            ->get();

        // Insert all batches into 'code_batches' table
        foreach ($existingBatches as $batch) {
            DB::table('code_batches')->insert(get_object_vars($batch));
        }

        // Add a column 'batch_id' in 'codes' table
        Schema::table('codes', function (Blueprint $table) {
            $table->integer('batch_id')->after('batch')->foreign('id')->on('code_batches');
        });

        // Apply 'batch_id' value in 'codes' table
        DB::table('codes')
            ->update([
                'batch_id' => DB::raw('(SELECT code_batches.id FROM code_batches WHERE code_batches.name = codes.batch)')
            ]);

        // remove column 'batch' in 'codes' table
        Schema::table('codes', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add a column 'batch' in 'codes' table
        Schema::table('codes', function (Blueprint $table) {
            $table->string('batch', 100)->after('batch_id');
        });

        // Apply 'batch' value in 'codes' table
        DB::table('codes')
            ->update([
                'batch' => DB::raw('(SELECT code_batches.name FROM code_batches WHERE code_batches.id = codes.batch_id)')
            ]);

        // remove column 'batch_id' in 'codes' table
        Schema::table('codes', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });

        Schema::drop('code_batches');
    }
}
