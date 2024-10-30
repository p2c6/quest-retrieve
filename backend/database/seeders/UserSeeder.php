<?php

namespace Database\Seeders;

use App\Models\Role;
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
                'id' => 1,
                'email' => 'test1@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 3,
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'email' => 'test2@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 3,
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'email' => 'test3@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 3,
                'created_at' => now(),
            ],
        ];

        User::insert($users);
    }
}
