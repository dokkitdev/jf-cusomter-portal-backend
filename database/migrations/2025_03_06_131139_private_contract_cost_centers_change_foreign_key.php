<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrivateContractCostCentersChangeForeignKey extends Migration
{
    public function up()
    {
        Schema::table('private_contract_cost_centers', function (Blueprint $table) {
            $table->dropForeign('private_contract_cost_centers_private_contract_id_foreign');

            $table
                ->foreign('private_contract_id')
                ->references('id')
                ->on('private_contracts')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('private_contract_cost_centers', function (Blueprint $table) {
            $table->dropForeign('private_contract_cost_centers_private_contract_id_foreign');

            $table
                ->foreign('private_contract_id')
                ->references('id')
                ->on('private_contracts')
                ->onDelete('set null');
        });
    }
}
