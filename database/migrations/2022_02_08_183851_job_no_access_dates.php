<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobNoAccessDates extends Migration
{
    public function up()
    {
        Schema::create('job_no_access_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_job_log_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_no_access_dates');
    }
}
