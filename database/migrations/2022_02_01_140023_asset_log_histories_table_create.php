<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetLogHistoriesTableCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_log_histories', function (Blueprint $table) {
            $table->id();
            $table->dateTime('assets_pulled_at');
            $table->integer('assets_count');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_log_histories');
    }
}
