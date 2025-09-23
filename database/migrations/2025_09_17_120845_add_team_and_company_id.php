<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamAndCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'assets',
            'customers',
            'jobs',
            'simpro_jobs',
            'sites',
        ];

        foreach ($tables as $item) {
            Schema::table($item, function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->after('id')->nullable();
                $table->unsignedBigInteger('company_id')->after('team_id')->nullable();

                $table->foreign('team_id')->references('id')->on('sim_pro_teams')->onDelete('cascade');
                $table->foreign('company_id')->references('id')->on('sim_pro_companies')->onDelete('cascade');;
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->after('id')->nullable();
            $table->foreign('team_id')->references('id')->on('sim_pro_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
