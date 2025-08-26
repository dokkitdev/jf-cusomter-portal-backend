<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ParsingLogsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('parsing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('parsing_type');
            $table->date('parsing_date');
            $table->integer('total_count');
            $table->integer('success_count');
            $table->jsonb('ids');
            $table->jsonb('error_reasons');
            $table->timestamps();

            $table->index('parsing_type');
            $table->index('parsing_date');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parsing_logs');
    }
}
