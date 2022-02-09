<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobsTableAddReportFields extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dateTime('logged_completion_date')->nullable();
            $table->foreignId('next_schedule_id')
                ->nullable()
                ->constrained()->references('id')->on('schedules')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'logged_completion_date',
                'next_schedule_id'
            ]);
        });
    }
}
