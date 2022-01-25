<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimproLogTableCreate extends Migration
{
    public function up()
    {
        Schema::create('simpro_log', function (Blueprint $table) {
            $table->id();
            $table->integer('loggable_id');
            $table->string('loggable_type');
            $table->enum('handle_status', ['new', 'error'])->default('new');
            $table->json('handle_result')->nullable();
            $table->timestamps();

            $table->unique(['loggable_id', 'loggable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('simpro_log');
    }
}
