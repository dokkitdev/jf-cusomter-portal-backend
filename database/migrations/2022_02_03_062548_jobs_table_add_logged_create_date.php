<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobsTableAddLoggedCreateDate extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dateTime('logged_create_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('logged_create_date');
        });
    }
}
