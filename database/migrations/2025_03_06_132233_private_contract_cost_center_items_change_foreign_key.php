<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrivateContractCostCenterItemsChangeForeignKey extends Migration
{
    public function up()
    {
        Schema::table('private_contract_cost_center_items', function (Blueprint $table) {
            $table->dropForeign('private_contract_cost_center_items_private_contract_cost_center');

            $table
                ->foreign('private_contract_cost_center_id')
                ->references('id')
                ->on('private_contract_cost_centers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('private_contract_cost_center_items', function (Blueprint $table) {
            $table->dropForeign('private_contract_cost_center_items_private_contract_cost_center');

            $table
                ->foreign('private_contract_cost_center_id')
                ->references('id')
                ->on('private_contract_cost_centers')
                ->onDelete('set null');
        });
    }
}
