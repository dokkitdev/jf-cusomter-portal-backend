<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsAddReportFields extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('asset_test_record_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('job_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('next_schedule_id')
                ->nullable()
                ->constrained()->references('id')->on('schedules')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->dateTime('no_access_date_1')->nullable();
            $table->dateTime('no_access_date_2')->nullable();
            $table->dateTime('no_access_date_3')->nullable();
            $table->dateTime('no_access_date_4')->nullable();
            $table->dateTime('no_access_date_5')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_test_record_id',
                'job_id',
                'customer_id',
                'next_schedule_id',
                'no_access_date_1',
                'no_access_date_2',
                'no_access_date_3',
                'no_access_date_4',
                'no_access_date_5',
            ]);
        });
    }
}
