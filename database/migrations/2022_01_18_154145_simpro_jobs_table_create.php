<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimproJobsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('simpro_jobs', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->enum('handle_status', ['new', 'error'])->default('new');
            $table->json('handle_result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('simpro_jobs');
    }
}
