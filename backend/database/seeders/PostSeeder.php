<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'id' => 1,
                'user_id' => 1,
                'subcategory_id' => 1,
                'name' => 'Test Post 1',
                'incident_location' => 'Quezon City',
                'incident_date' => '2024-05-06',
                'finish_transaction_date' => '2024-05-07',
                'expiration_date' =>  '2024-06-06',
                'status' => 'finish',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'subcategory_id' => 2,
                'name' => 'Test Post 2',
                'incident_location' => 'Manila City',
                'incident_date' => '2024-05-06',
                'finish_transaction_date' => '2024-05-07',
                'expiration_date' =>  '2024-06-06',
                'status' => 'finish',
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'subcategory_id' => 3,
                'name' => 'Test Post 3',
                'incident_location' => 'Pasay City',
                'incident_date' => '2024-05-06',
                'finish_transaction_date' => '2024-05-07',
                'expiration_date' =>  '2024-06-06',
                'status' => 'finish',
            ],
        ];

        Post::insert($posts);
    }
}
