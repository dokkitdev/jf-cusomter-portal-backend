<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotifyAssetReportValidationsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('notify_asset_report_validations', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id');
            $table->integer('asset_id');
            $table->foreignId('asset_report_id');
            $table->enum('error_type', [
                'last_service_over_14_months',
                'service_due_in_30_days',
                'service_due_tomorrow',
                'service_complete_outside_dude_date',
                'no_uprn',
                'no_fuel_type',
                'no_asset_make',
                'no_model',
            ]);
            $table->text('error_text');
            $table->string('uprn')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('service_level_name')->nullable();
            $table->string('job_stage')->nullable();
            $table->timestamps();

            $table->foreign('asset_report_id')
                ->references('id')
                ->on('notify_asset_reports')
                ->cascadeOnDelete()
                ->restrictOnUpdate();

            $table->index(['asset_report_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notify_asset_report_validations');
    }
}
