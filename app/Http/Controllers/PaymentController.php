<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Order;


class PaymentController extends Controller
{
   /**
    * Display a listing of the resource.
    */
   public function checkout(Request $request)
   {
      $user = $request->user();
      $orderId = $request->orderId;

      if (!$orderId) {
         return response()->json(['error' => 'Order ID is required'], 400);
      }

      $order = Order::with('products')->find($orderId);

      if (!$order) {
         return response()->json(['error' => 'Order not found'], 404);
      }

      // Préparer les items pour Stripe Checkout
      $lineItems = $order->products->map(function ($product) {
         return [
            'price_data' => [
               'currency' => env('CASHIER_CURRENCY', 'eur'),
               'product_data' => [
                  'name' => $product->name,
               ],
               'unit_amount' => $product->price, // Convertir en centimes
            ],
            'quantity' => $product->pivot->quantity, // Quantité commandée
         ];
      })->toArray();

      // Création d'une session Stripe Checkout
      $session = $user->checkout(
         $lineItems,
         [
            'success_url' => env('FRONTEND_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('FRONTEND_URL') . '/payment/cancel',
         ]
      );

      return response()->json(['id' => $session->id]);
   }

   public function index()
   {
      //
   }

   /**
    * Show the form for creating a new resource.
    */
   public function create()
   {
      //
   }

   /**
    * Store a newly created resource in storage.
    */
   public function store(Request $request)
   {
      //
   }

   /**
    * Display the specified resource.
    */
   public function show(Payment $payment)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    */
   public function edit(Payment $payment)
   {
      //
   }

   /**
    * Update the specified resource in storage.
    */
   public function update(Request $request, Payment $payment)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    */
   public function destroy(Payment $payment)
   {
      //
   }
}
