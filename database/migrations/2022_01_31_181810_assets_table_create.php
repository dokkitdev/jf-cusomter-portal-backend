<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->integer('simpro_asset_id')->unique();
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('location')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->date('last_test_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('last_test_result')->nullable();
            $table->string('service_level_name')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
