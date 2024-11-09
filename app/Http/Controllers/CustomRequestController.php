<?php

namespace App\Http\Controllers;

use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomRequestController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $customRequests = CustomRequest::with(['images', 'category.images'])->get();
    return response()->json($customRequests);
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
      'phone' => 'nullable|string|max:15',
      'message' => 'required|string|max:1000',
      'category_id' => 'required|exists:categories,id',
      'images.*' => 'image|max:10240', // 10MB max pour chaque image
    ]);

    // Vérifier si la validation échoue
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Création de la demande personnalisée
    $customRequest = CustomRequest::create([
      'email' => Auth::user()->email,
      'phone' => $request->phone,
      'message' => $request->message,
      'category_id' => $request->category_id,
      'user_id' => $request->user()->id,
    ]);

    // Gestion de l'upload des images
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $path = $image->store('custom_requests', 's3');
        $url = Storage::disk('s3')->url($path);

        $customRequest->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }

    return response()->json($customRequest->load('images'), 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(CustomRequest $customRequest)
  {
    $customRequest->load(['images', 'category.images']);
    return response()->json($customRequest);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(CustomRequest $customRequest)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, CustomRequest $customRequest): JsonResponse
  {
    // Vérifier que l'utilisateur connecté est le propriétaire de la demande
    if ($customRequest->user_id !== Auth::id()) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validator = Validator::make($request->all(), [
      'phone' => 'nullable|string|max:15',
      'message' => 'sometimes|string|max:1000',
      'category_id' => 'sometimes|exists:categories,id',
      'images.*' => 'image|max:10240', // 10MB
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mise à jour de la demande personnalisée
    $customRequest->update([
      'email' => Auth::user()->email, // Utiliser l'email de l'utilisateur connecté
      'phone' => $request->phone ?? $customRequest->phone,
      'message' => $request->message ?? $customRequest->message,
      'category_id' => $request->category_id ?? $customRequest->category_id,
    ]);

    // Gestion des nouvelles images
    if ($request->hasFile('images')) {
      // Supprimer les anciennes images
      foreach ($customRequest->images as $image) {
        Storage::disk('s3')->delete($image->url);
        $image->delete();
      }

      // Ajouter les nouvelles images
      foreach ($request->file('images') as $image) {
        $path = $image->store('custom_requests', 's3');
        $url = Storage::disk('s3')->url($path);

        $customRequest->images()->create([
          'url' => $url,
          'type' => 'public',
        ]);
      }
    }

    return response()->json($customRequest->load('images'), 200);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(CustomRequest $customRequest): JsonResponse
  {
    // Vérifier que l'utilisateur connecté est le propriétaire de la demande
    if ($customRequest->user_id !== Auth::id()) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Supprimer les images associées
    foreach ($customRequest->images as $image) {
      Storage::disk('s3')->delete($image->url);
      $image->delete();
    }

    // Supprimer la demande personnalisée
    $customRequest->delete();

    return response()->json(['message' => 'Custom request deleted'], 200);
  }
}
