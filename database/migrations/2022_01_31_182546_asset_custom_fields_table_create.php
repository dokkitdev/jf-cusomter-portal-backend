<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetCustomFieldsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_custom_field_id');
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();

            $table->unique([
                'asset_id',
                'simpro_custom_field_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_custom_fields');
    }
}
