<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimproJobsTableAddHandleStatusIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simpro_jobs', function (Blueprint $table) {
            $table->index(['handle_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simpro_jobs', function (Blueprint $table) {
            $table->dropIndex(['handle_status']);
        });
    }
}
