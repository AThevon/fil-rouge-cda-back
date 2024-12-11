<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderProduct;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
   /**
    * Display a listing of the resource.
    */
   public function index(): JsonResponse
   {
      $orders = Order::with('orderProducts.product.images')
         ->where('user_id', Auth::id())
         ->orderBy('created_at', 'desc')
         ->get();

      return response()->json($orders);
   }

   public function store(Request $request): JsonResponse
   {
      $validator = Validator::make($request->all(), [
         'products' => 'required|array',
         'products.*.id' => 'required|exists:products,id',
         'products.*.quantity' => 'required|integer|min:1',
         'products.*.price' => 'required|integer|min:0',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      $totalPrice = 0;
      foreach ($request->products as $productData) {
         $totalPrice += $productData['price'] * $productData['quantity'];
      }

      $order = Order::create([
         'user_id' => Auth::id(),
         'total_price' => $totalPrice,
         'status' => OrderStatus::PENDING,
      ]);

      foreach ($request->products as $productData) {
         OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $productData['id'],
            'quantity' => $productData['quantity'],
            'price' => $productData['price'],
         ]);
      }

      $order->load('orderProducts.product.images');

      return response()->json(['order' => $order], 201);
   }

   public function show(Order $order)
   {
      $order->load('orderProducts.product.images');
      return response()->json($order);
   }

}
