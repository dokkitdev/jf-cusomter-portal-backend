<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AuthenticationCodesTableCreate extends Migration
{
    public function up(): void
    {
        Schema::create('authentication_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authentication_codes');
    }
}
