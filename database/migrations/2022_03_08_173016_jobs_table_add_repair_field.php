<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobsTableAddRepairField extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('is_repair')->default(false);
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('is_repair');
        });
    }
}
