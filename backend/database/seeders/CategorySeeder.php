<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Appliance'
            ],
            [
                'id' => 2,
                'name' => 'Gadget'
            ],
            [
                'id' => 3,
                'name' => 'Clothes'
            ],
        ];

        Category::insert($categories);
    }
}
