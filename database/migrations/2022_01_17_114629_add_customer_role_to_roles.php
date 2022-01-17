<?php

use Illuminate\Database\Migrations\Migration;

class AddCustomerRoleToRoles extends Migration
{
    public function up()
    {
        DB::table('roles')->insert([[
            'id' => 3,
            'name' => 'customer'
        ]]);
    }

    public function down()
    {
        DB::table('roles')->delete(3);
    }
}
