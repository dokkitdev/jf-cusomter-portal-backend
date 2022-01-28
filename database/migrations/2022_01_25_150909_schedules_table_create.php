<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SchedulesTableCreate extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_schedule_id');
            $table->string('name')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'simpro_schedule_id',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
