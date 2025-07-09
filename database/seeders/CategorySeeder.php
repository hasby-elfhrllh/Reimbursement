<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transportasi',
                'limit_per_month' => 1000000,
            ],
            [
                'name' => 'Kesehatan',
                'limit_per_month' => 2000000,
            ],
            [
                'name' => 'Makan',
                'limit_per_month' => 1500000,
            ],
            [
                'name' => 'Lainnya',
                'limit_per_month' => 500000,
            ],
        ];

        foreach ($categories as $data) {
            Category::create($data);
        }
    }
}
