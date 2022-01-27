<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobWorkOrdersTableCreate extends Migration
{
    public function up()
    {
        Schema::create('job_work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_section_id');
            $table->integer('simpro_cost_center_id');
            $table->integer('simpro_work_order_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'simpro_section_id',
                'simpro_cost_center_id',
                'simpro_work_order_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_work_orders');
    }
}
