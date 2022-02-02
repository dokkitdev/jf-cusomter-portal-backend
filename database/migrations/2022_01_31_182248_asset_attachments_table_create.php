<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetAttachmentsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('simpro_attachment_id');
            $table->string('name');
            $table->timestamps();

            $table->unique([
                'asset_id',
                'simpro_attachment_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_attachments');
    }
}
