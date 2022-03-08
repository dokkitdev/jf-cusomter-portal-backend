<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableAddExpiryDateField extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->date('expiry_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('expiry_date');
        });
    }
}
