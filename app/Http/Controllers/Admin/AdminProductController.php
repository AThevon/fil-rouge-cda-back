<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminProductController extends Controller
{
   public function index(): View
   {
      $products = Product::with(['category.images', 'images'])
         ->withCount('productVotes as vote_count')
         ->orderBy('created_at', 'desc')
         ->get();

      return view('admin.products.index', compact('products'));
   }

   public function create(): View
   {
      return view('admin.products.create');
   }

   public function store(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'description' => 'required|string',
         'price' => 'required|numeric|min:0',
         'stock' => 'required|integer|min:0',
         'category_id' => 'required|exists:categories,id',
         'images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB
      ]);

      if ($validator->fails()) {
         return redirect()->back()
            ->withErrors($validator)
            ->withInput();
      }

      $product = Product::create($validator->validated());

      if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
            $path = $image->store('products', 's3');
            $url = Storage::disk('s3')->url($path);

            $product->images()->create([
               'url' => $url,
               'type' => 'public',
            ]);
         }
      }

      return redirect()->route('admin.products.index')
         ->with('success', 'Product created successfully!');
   }

   public function edit(Product $product): View
   {
      return view('admin.products.edit', compact('product'));
   }

   public function update(Request $request, Product $product)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'sometimes|required|string|max:255',
         'description' => 'sometimes|required|string',
         'price' => 'sometimes|required|numeric|min:0',
         'stock' => 'sometimes|required|integer|min:0',
         'category_id' => 'sometimes|required|exists:categories,id',
         'images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB
      ]);

      if ($validator->fails()) {
         return redirect()->back()
            ->withErrors($validator)
            ->withInput();
      }

      $product->update($validator->validated());

      if ($request->hasFile('images')) {
         foreach ($product->images as $image) {
            Storage::disk('s3')->delete($image->url);
            $image->delete();
         }

         foreach ($request->file('images') as $image) {
            $path = $image->store('products', 's3');
            $url = Storage::disk('s3')->url($path);

            $product->images()->create([
               'url' => $url,
               'type' => 'public',
            ]);
         }
      }

      return redirect()->route('admin.products.index')
         ->with('success', 'Product updated successfully!');
   }

   public function destroy(Product $product)
   {
      if ($product->orders()->exists()) {
         return redirect()->route('admin.products.index')
            ->with('error', 'Cannot delete product with existing orders.');
      }

      foreach ($product->images as $image) {
         Storage::disk('s3')->delete($image->url);
         $image->delete();
      }

      $product->delete();

      return redirect()->route('admin.products.index')
         ->with('success', 'Product deleted successfully!');
   }
}