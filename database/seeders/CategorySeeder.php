<?php

namespace Database\Seeders;

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
            ['name' => 'Electronics', 'image' => 'electronics.jpg'],
            ['name' => 'Books', 'image' => 'books.jpg'],
            ['name' => 'Clothing', 'image' => 'clothing.jpg'],
            ['name' => 'Home & Kitchen', 'image' => 'home_kitchen.jpg'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}