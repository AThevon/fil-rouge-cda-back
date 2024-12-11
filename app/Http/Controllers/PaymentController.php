<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
   public function store(Request $request)
   {
      // Validation des données entrantes
      $validator = Validator::make($request->all(), [
         'orderId' => 'required|exists:orders,id',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      // Récupération de la commande
      $order = Order::with('products.images')->find($request->orderId);

      // Vérification du statut de la commande
      if ($order->status !== 'pending') {
         return response()->json(['error' => 'Cannot process payment for this order'], 400);
      }

      // Préparation des articles pour Stripe Checkout
      $lineItems = $order->products->map(function ($product) {
         $image = $product->images->firstWhere('type', 'public')?->url;

         return [
            'price_data' => [
               'currency' => env('CASHIER_CURRENCY', 'eur'),
               'product_data' => [
                  'name' => $product->name,
                  'images' => $image ? [$image] : [],
               ],
               'unit_amount' => $product->price,
            ],
            'quantity' => $product->pivot->quantity,
         ];
      })->toArray();

      // Création de la session Stripe
      $session = $request->user()->checkout(
         $lineItems,
         [
            'success_url' => env('FRONTEND_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('FRONTEND_URL') . '/payment/cancel',
         ]
      );

      // Retour de l'ID de la session Stripe
      return response()->json(['id' => $session->id]);
   }
}
