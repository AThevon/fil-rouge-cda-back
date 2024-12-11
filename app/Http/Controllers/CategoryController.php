<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
   public function index(): JsonResponse
   {
      $categories = Category::with('images')->orderBy('created_at', 'desc')->get();
      return response()->json($categories);
   }
}
