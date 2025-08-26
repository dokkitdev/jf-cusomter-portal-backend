<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotifyCsvReportsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('notify_csv_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('file_name');
            $table->boolean('is_finished');
            $table->timestamps();

            $table->index('report_type');
            $table->index('file_name');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notify_csv_reports');
    }
}
