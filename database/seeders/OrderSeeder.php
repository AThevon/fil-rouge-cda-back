<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Exemple d'utilisateurs et produits fictifs
        $users = DB::table('users')->pluck('id'); // Assurez-vous d'avoir des utilisateurs existants
        $products = DB::table('products')->get(); // Assurez-vous d'avoir des produits existants

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Aucun utilisateur ou produit disponible. Ajoutez-les avant de lancer ce seeder.');
            return;
        }

        foreach ($users as $userId) {
            // Créer une commande
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'total_price' => 0, // Calculé ensuite
                'status' => OrderStatus::PENDING->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ajouter des produits à cette commande
            $totalPrice = 0;
            foreach ($products->random(2) as $product) { // Prendre 2 produits au hasard
                $quantity = rand(1, 5);
                $price = $product->price; // Assurez-vous que `price` existe dans votre table produits
                $totalPrice += $price * $quantity;

                DB::table('order_products')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Mettre à jour le total_price de la commande
            DB::table('orders')->where('id', $orderId)->update(['total_price' => $totalPrice]);
        }
    }
}
