<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetTestRecordsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_test_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('job_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->date('test_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_test_records');
    }
}
