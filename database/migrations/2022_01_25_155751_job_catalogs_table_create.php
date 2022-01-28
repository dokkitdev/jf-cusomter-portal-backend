<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobCatalogsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('job_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_section_id');
            $table->integer('simpro_cost_center_id');
            $table->integer('simpro_catalog_id');
            $table->integer('simpro_original_catalog_id')->nullable();
            $table->text('name')->nullable();
            $table->string('part_no')->nullable();
            $table->decimal('qty')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'simpro_section_id',
                'simpro_cost_center_id',
                'simpro_catalog_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_catalogs');
    }
}
