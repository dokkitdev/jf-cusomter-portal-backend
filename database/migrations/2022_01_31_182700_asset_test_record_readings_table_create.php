<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetTestRecordReadingsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_test_record_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_test_record_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_test_record_readings');
    }
}
