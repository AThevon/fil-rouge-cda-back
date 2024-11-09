<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVote;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(): JsonResponse
  {
    // Utilisation de withCount pour inclure le nombre de votes
    $products = Product::with(['category.images', 'images'])
      ->withCount('productVotes as vote_count')
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json($products);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'description' => 'required|string',
      'price' => 'required|numeric|min:0',
      'stock' => 'required|integer|min:0',
      'category_id' => 'required|exists:categories,id',
      'images.*' => 'required|file|mimes:jpg,jpeg,png|max:10240', // 10MB
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Création du produit
    $product = Product::create($validator->validated());

    // Gestion des images
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $path = $image->store('products', 's3');
        $url = Storage::disk('s3')->url($path);

        // Création de l'image associée au produit
        $product->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }

    return response()->json($product->load('images'), 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Product $product): JsonResponse
  {
    $product->load(['category.images', 'images'])->loadCount('productVotes as vote_count');

    return response()->json($product);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Product $product): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|required|string|max:255',
      'description' => 'sometimes|required|string',
      'price' => 'sometimes|required|numeric|min:0',
      'stock' => 'sometimes|required|integer|min:0',
      'category_id' => 'sometimes|required|exists:categories,id',
      'images.*' => 'image|max:10240',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mise à jour du produit
    $product->update($validator->validated());

    // Gestion des nouvelles images
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $path = $image->store('products', 's3');
        $url = Storage::disk('s3')->url($path);

        // Ajout de l'image
        $product->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }

    return response()->json($product->load('images'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Product $product): JsonResponse
  {
    if ($product->orders()->exists()) {
      return response()->json(['message' => 'Product has orders'], 409);
    }

    // Supprimer les images associées
    foreach ($product->images as $image) {
      Storage::disk('s3')->delete($image->url);
      $image->delete();
    }

    $product->delete();
    return response()->json(['message' => 'Product deleted'], 200);
  }

  /**
   * Toggle vote for the specified resource.
   */
  public function toggleVote(Product $product): JsonResponse
  {
    $user = Auth::user();

    if (!$user) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Vérifier si l'utilisateur a déjà voté pour ce produit
    $existingVote = ProductVote::where('product_id', $product->id)
      ->where('user_id', $user->id)
      ->first();

    if ($existingVote) {
      // Retirer le vote
      $existingVote->delete();
      $product->refresh();

      return response()->json([
        'is_upvoted' => false,
        'message' => 'Vote removed',
        'votes' => $product->vote_count
      ], 200);
    } else {
      // Ajouter un vote
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