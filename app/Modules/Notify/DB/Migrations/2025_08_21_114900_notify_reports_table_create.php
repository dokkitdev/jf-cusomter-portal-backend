<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotifyReportsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('notify_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->integer('letters_generated');
            $table->boolean('is_finished');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notify_reports');
    }
}
