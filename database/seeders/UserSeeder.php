<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'bodyast1996@gmail.com',
            'password' =>  Hash::make('12345678'),
            'role_id' => Role::USER,
        ]);
//
//        factory(User::class)->create([
//            'role_id' => Role::USER
//        ]);
    }
}
