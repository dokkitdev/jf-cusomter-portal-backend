<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableAddReportFields extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->date('last_cp12_date')->nullable();
            $table->integer('asset_type')->nullable();
            $table->string('custom_asset_type_value')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_type',
                'last_cp12_date',
                'custom_asset_type_value'
            ]);
        });
    }
}
