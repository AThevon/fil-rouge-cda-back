<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVote;

class ProductController extends Controller
{
   public function index(): JsonResponse
   {
      $products = Product::with(['category.images', 'images'])
         ->withCount('productVotes as vote_count')
         ->orderBy('created_at', 'desc')
         ->get();

      return response()->json($products);
   }

   public function show(Product $product): JsonResponse
   {
      $product->load(['category.images', 'images'])->loadCount('productVotes as vote_count');

      return response()->json($product);
   }

   public function toggleVote(Product $product): JsonResponse
   {
      $user = Auth::user();

      $existingVote = ProductVote::where('product_id', $product->id)
         ->where('user_id', $user->id)
         ->first();

      if ($existingVote) {
         $existingVote->delete();
         $product->refresh();

         return response()->json([
            'is_upvoted' => false,
            'message' => 'Vote removed',
            'votes' => $product->vote_count
         ], 200);
      } else {
         ProductVote::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
         ]);
         $product->refresh();

         return response()->json([
            'is_upvoted' => true,
            'message' => 'Vote added',
            'votes' => $product->vote_count
         ], 200);
      }
   }
}