<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SiteContactsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('site_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('simpro_contact_id');
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('email')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique([
                'site_id',
                'simpro_contact_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_contacts');
    }
}
