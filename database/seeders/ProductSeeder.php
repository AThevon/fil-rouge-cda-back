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
            'name' => 'Les Guns',
            'description' => 'Symbole du fameux groupe Guns N\'Roses en bois naturel plaqué sur une plaque en bois noir',
            'price' => 8000,
            'stock' => 5,
            'category_id' => Category::where('name', CategoryName::FRAME->value)->first()->id,
            'images' => [
               'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_1.png',
            ],
         ],
         [
            'name' => 'Roi de la savane',
            'description' => 'Le roi de la savane, chantourné directement sur une plaque en bois vernis',
            'price' => 4000,
            'stock' => 10,
            'category_id' => Category::where('name', CategoryName::TOTEM->value)->first()->id,
            'images' => [
               'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_2.png',
            ],
         ],
         [
            'name' => 'Mont Fuji',
            'description' => 'Grand symbole du pays du soleil levant, le Mont Fuji chantourné sur une plaque en bois naturel',
            'price' => 5000,
            'stock' => 20,
            'category_id' => Category::where('name', CategoryName::LEDS->value)->first()->id,
            'images' => [
               'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_3.png',
            ],
         ],
         [
            'name' => 'Ying-Yang félin',
            'description' => 'Un ying et yang des plus félins, sous la forme d\'un totem en bois vernis',
            'price' => 6000,
            'stock' => 8,
            'category_id' => Category::where('name', CategoryName::TOTEM->value)->first()->id,
            'images' => [
               'https://woodies-s3-bucket.s3.eu-west-3.amazonaws.com/products/item_4.png',
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