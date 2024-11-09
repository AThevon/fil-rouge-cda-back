<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Enums\CategoryName;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      ['name' => CategoryName::FRAME->value, 'image_urls' => ['https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/categories/frame.png']],
      ['name' => CategoryName::TOTEM->value, 'image_urls' => ['https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/categories/totem.png']],
      ['name' => CategoryName::LEDS->value, 'image_urls' => ['https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/categories/leds.png']],
    ];

    foreach ($categories as $categoryData) {
      $imageUrls = $categoryData['image_urls'];
      unset($categoryData['image_urls']);

      // Création de la catégorie
      $category = Category::create($categoryData);

      // Ajout des images
      foreach ($imageUrls as $url) {
        $category->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }
  }
}