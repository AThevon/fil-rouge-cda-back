<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Js;

class CategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(): JsonResponse
  {
    $categories = Category::with('images')->orderBy('created_at', 'desc')->get();
    return response()->json($categories);
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
  public function store(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'images.*' => 'required|file|mimes:jpg,jpeg,png|max:10240', // 10MB
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Création de la catégorie
    $category = Category::create(['name' => $request->name]);

    // Gestion des images
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $path = $image->store('categories', 's3');
        $url = Storage::disk('s3')->url($path);

        // Ajout de l'image via la relation polymorphique
        $category->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }

    return response()->json($category->load('images'), 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Category $category): JsonResponse
  {
    return response()->json($category->load('images'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Category $category)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Category $category): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|required|string|max:255',
      'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mise à jour du nom de la catégorie
    if ($request->has('name')) {
      $category->update(['name' => $request->name]);
    }

    // Gestion de l'upload de la nouvelle image
    if ($request->hasFile('image')) {
      // Supprimer l'ancienne image s'il y en a une
      if ($category->images()->exists()) {
        $oldImage = $category->images()->first();
        Storage::disk('s3')->delete($oldImage->url);
        $oldImage->delete();
      }

      // Uploader la nouvelle image
      $path = $request->file('image')->store('categories', 's3');
      $url = Storage::disk('s3')->url($path);

      // Créer la nouvelle image associée à la catégorie
      $category->images()->create([
        'url' => $url,
        'type' => 'public',
      ]);
    }

    // Charger la catégorie avec l'image pour la réponse
    $category->load('images');

    return response()->json($category);
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Category $category)
  {
    //
  }
}
