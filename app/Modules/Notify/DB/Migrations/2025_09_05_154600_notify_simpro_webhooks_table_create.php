<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotifySimproWebhooksTableCreate extends Migration
{
    public function up(): void
    {
        Schema::create('notify_simpro_webhooks', function (Blueprint $table) {
            $table->id();
            $table->jsonb('data');
            $table->enum('status', ['pending', 'processing', 'success', 'failed']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notify_simpro_webhooks');
    }
}
