<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Microwave'
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'name' => 'Phone'
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'name' => 'Headset'
            ],
            [
                'id' => 4,
                'category_id' => 3,
                'name' => 'Polo'
            ],
        ];

        Subcategory::insert($subcategories);
    }
}
