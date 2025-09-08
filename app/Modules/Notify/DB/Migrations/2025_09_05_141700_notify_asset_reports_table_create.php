<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotifyAssetReportsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('notify_asset_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id');
            $table->integer('asset_id');
            $table->string('uprn')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('type')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('service_level_start_date')->nullable();
            $table->date('job_due_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('job_stage')->nullable();
            $table->string('service_level_name')->nullable();
            $table->string('last_mot_date')->nullable();
            $table->date('service_due')->nullable();
            $table->string('next_scheduled_appointment_date')->nullable();
            $table->string('no_access_visits')->nullable();
            $table->string('location')->nullable();
            $table->string('cancellation')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'asset_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notify_asset_reports');
    }
}
