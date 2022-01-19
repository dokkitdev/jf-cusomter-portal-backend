<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Traits\MigrationTrait;

class CustomersTableCreate extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('simpro_customer_id');
            $table->string('name');
            $table->enum('type', ['individuals', 'companies']);
            $table->timestamps();
        });

        $this->createBridgeTable('customer', 'user');
    }

    public function down()
    {
        $this->dropBridgeTable('customer', 'user');

        Schema::dropIfExists('customers');
    }
}
