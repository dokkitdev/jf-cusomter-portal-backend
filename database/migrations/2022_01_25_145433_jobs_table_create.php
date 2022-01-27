<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('simpro_job_id')->unique();
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('order_no')->nullable();
            $table->text('description')->nullable();
            $table->string('priority')->nullable();
            $table->string('cost_center_name')->nullable();
            $table->string('stage')->nullable();
            $table->string('job_status')->nullable();
            $table->date('date_created')->nullable();
            $table->dateTime('made_safe_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
