<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "username" => "LEYS-1001",
                "email" => "david.kariuki@leysco.co.ke",
                "password" => "SecurePass123!",
                "first_name" => "David",
                "last_name" => "Kariuki",
                "role" => "Sales Manager",
                "status" => "active"
            ],
            [
                "username" => "LEYS-1002",
                "email" => "jane.njoki@leysco.co.ke",
                "password" => "SecurePass456!",
                "first_name" => "Jane",
                "last_name" => "Njoki",
                "role" => "Sales Representative",
                "status" => "active"
            ],
        ];

        foreach ($users as $user) {
            $new_user = User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $user['password'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                "status" => "active"
            ]);

            $new_user->assignRole($user['role']);
        }
    }
}
