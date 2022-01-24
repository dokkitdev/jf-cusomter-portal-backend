<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Traits\MigrationTrait;

class SitesTableCreate extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->integer('simpro_site_id')->unique();
            $table->string('name');
            $table->string('uprn')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        $this->createBridgeTable('customer', 'site');
    }

    public function down()
    {
        $this->dropBridgeTable('customer', 'site');

        Schema::dropIfExists('sites');
    }
}
