<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Traits\MigrationTrait;

class PrivateContractCostCentersCreateTable extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('private_contract_cost_centers', function (Blueprint $table) {
            $table->increments('id');
            $table
                ->foreignId('private_contract_id')
                ->nullable()
                ->constrained('private_contracts')
                ->nullOnDelete();
            $table->integer('simpro_section_id');
            $table->decimal('ex_tax',)->nullable();
            $table->decimal('tax')->nullable();
            $table->decimal('inc_tax')->nullable();
            $table->string('section_name')->nullable();
            $table->string('name');
            $table->timestamps();

            $table->index(['private_contract_id', 'simpro_section_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_contract_cost_centers');
    }
}
