<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MultiTeam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_pro_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('build_url');
            $table->string('token')->nullable();
            $table->string('api_key')->nullable();
            $table->timestamps();
        });

        Schema::create('sim_pro_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sim_pro_team_id');
            $table->integer('sim_pro_company_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();

            $table
                ->foreign('sim_pro_team_id')
                ->references('id')
                ->on('sim_pro_teams')
                ->onDelete('cascade');
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
