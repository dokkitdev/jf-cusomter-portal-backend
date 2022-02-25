<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimproJobsTableAddSimproEntityIdField extends Migration
{
    public function up()
    {
        Schema::table('simpro_jobs', function (Blueprint $table) {
            $table->integer('simpro_entity_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('simpro_jobs', function (Blueprint $table) {
            $table->dropColumn(['simpro_entity_id']);
        });
    }
}
