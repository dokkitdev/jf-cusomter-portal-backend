<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Traits\MigrationTrait;

class PrivateContractCostCenterItemsCreateTable extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('private_contract_cost_center_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('simpro_recurring_invoice_cost_center_item_id')->nullable();
            $table
                ->foreignId('private_contract_cost_center_id')
                ->nullable()
                ->constrained('private_contract_cost_centers')
                ->nullOnDelete();
            $table->integer('qty')->nullable();
            $table->decimal('ex_tax')->nullable();
            $table->decimal('inc_tax')->nullable();
            $table->string('name')->nullable();
            $table->enum('type', [
                'Catalogs',
                'OneOffs',
                'Prebuilds',
                'Discount',
            ]);
            $table->integer('order');
            $table->timestamps();

            $table->index(['private_contract_cost_center_id', 'simpro_recurring_invoice_cost_center_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_contract_cost_center_items');
    }
}
