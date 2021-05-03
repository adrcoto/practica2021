<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@practica.ro";
        $user->password = Hash::make("admin");
        $user->status = User::STATUS_ACTIVE;
        $user->role_id = Role::ROLE_ADMIN;

        $user->save();
    }
}
