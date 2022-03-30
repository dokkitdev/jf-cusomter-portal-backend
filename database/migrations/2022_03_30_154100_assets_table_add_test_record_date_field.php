<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableAddTestRecordDateField extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->date('test_record_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('test_record_date');
        });
    }
}
