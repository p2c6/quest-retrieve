<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
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
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 1,
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'email' => 'moderator@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 2,
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'email' => 'user@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'role_id' => 3,
                'created_at' => now(),
            ],
        ];

        User::insert($users);

        $profiles = [
            [
                    'id' => 1,
                    'user_id' => 1,
                    'last_name' => 'Doe',
                    'first_name' => 'John',
                    'contact_no' => '0905482331',
                    'birthday' => '2025-04-06',
                    'avatar' => null,
                    'profile_date_updated' => now()->format('Y-m-d'),
                    'created_at' => now()
            ],
            [
                    'id' => 2,
                    'user_id' => 2,
                    'last_name' => 'Max',
                    'first_name' => 'Gin',
                    'contact_no' => '0905482331',
                    'birthday' => '2013-03-04',
                    'avatar' => null,
                    'profile_date_updated' => now()->format('Y-m-d'),
                    'created_at' => now()
            ],
            [
                    'id' => 3,
                    'user_id' => 3,
                    'last_name' => 'Kleber',
                    'first_name' => 'Ronny',
                    'contact_no' => '0905482331',
                    'birthday' => '2005-02-27',
                    'avatar' => null,
                    'profile_date_updated' => now()->format('Y-m-d'),
                    'created_at' => now()
            ]
        ];

        Profile::insert($profiles);
    }
}
