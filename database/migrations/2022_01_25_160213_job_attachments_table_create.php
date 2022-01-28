<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobAttachmentsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('job_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('simpro_attachment_id');
            $table->string('name');
            $table->dateTime('date_added')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'simpro_attachment_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_attachments');
    }
}
