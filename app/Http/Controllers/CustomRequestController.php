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
   public function index()
   {
      $customRequests = CustomRequest::with(['images', 'category.images'])
         ->where('user_id', Auth::id())
         ->orderBy('created_at', 'desc')
         ->get();

      return response()->json($customRequests);
   }

   public function store(Request $request): JsonResponse
   {
      $validator = Validator::make($request->all(), [
         'phone' => 'nullable|string|max:15',
         'message' => 'required|string|max:1000',
         'category_id' => 'required|exists:categories,id',
         'images.*' => 'image|max:10240', // 10MB
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      $customRequest = CustomRequest::create([
         'email' => Auth::user()->email,
         'phone' => $request->phone,
         'message' => $request->message,
         'category_id' => $request->category_id,
         'user_id' => $request->user()->id,
      ]);

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

   public function show(CustomRequest $customRequest)
   {
      if ($customRequest->user_id !== Auth::id()) {
         return response()->json(['message' => 'Unauthorized'], 403);
      }

      $customRequest->load(['images', 'category.images']);
      return response()->json($customRequest);
   }
}
