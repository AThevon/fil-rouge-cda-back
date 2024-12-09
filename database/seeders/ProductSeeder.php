<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Enums\CategoryName;

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
        'price' => 59999,
        'stock' => 50,
        'category_id' => Category::where('name', CategoryName::FRAME->value)->first()->id,
        'images' => [
          'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_1.png',
        ],
      ],
      [
        'name' => 'Fiction Novel',
        'description' => 'A bestselling fiction novel.',
        'price' => 1599,
        'stock' => 100,
        'category_id' => Category::where('name', CategoryName::TOTEM->value)->first()->id,
        'images' => [
          'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_2.png',
        ],
      ],
      [
        'name' => 'T-Shirt',
        'description' => 'A comfortable cotton T-Shirt.',
        'price' => 1999,
        'stock' => 200,
        'category_id' => Category::where('name', CategoryName::LEDS->value)->first()->id,
        'images' => [
          'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_3.png',
        ],
      ],
    ];

    foreach ($products as $productData) {
      $images = $productData['images'];
      unset($productData['images']);

      // Création du produit
      $product = Product::create($productData);

      // Ajout des images
      foreach ($images as $imageUrl) {
        $product->images()->create([
          'url' => $imageUrl,
          'type' => 'public',
        ]);
      }
    }
  }
}