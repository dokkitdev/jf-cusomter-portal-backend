<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrivateContractAddFieldForDocs extends Migration
{
    public function up()
    {
        Schema::table('private_contracts', function (Blueprint $table) {
            $table->string('docx')->nullable();
            $table->string('pdf')->nullable();
        });
    }

    public function down()
    {
        Schema::table('private_contracts', function (Blueprint $table) {
            $table->dropColumn('docx');
            $table->dropColumn('pdf');
        });
    }
}
