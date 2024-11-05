<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone',
                'description' => 'A powerful smartphone with 128GB storage.',
                'price' => 599.99,
                'stock' => 50,
                'category_id' => Category::where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => 'Fiction Novel',
                'description' => 'A bestselling fiction novel.',
                'price' => 15.99,
                'stock' => 100,
                'category_id' => Category::where('name', 'Books')->first()->id,
            ],
            [
                'name' => 'T-Shirt',
                'description' => 'A comfortable cotton T-Shirt.',
                'price' => 19.99,
                'stock' => 200,
                'category_id' => Category::where('name', 'Clothing')->first()->id,
            ],
            [
                'name' => 'Blender',
                'description' => 'A high-speed blender for smoothies.',
                'price' => 89.99,
                'stock' => 30,
                'category_id' => Category::where('name', 'Home & Kitchen')->first()->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}