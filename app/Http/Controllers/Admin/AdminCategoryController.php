<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminCategoryController extends Controller
{
   public function index()
   {
      $categories = Category::all();

      return view('admin.categories.index', compact('categories'));
   }

   public function create()
   {
      return view('admin.categories.create');
   }

   public function store(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB
      ]);

      if ($validator->fails()) {
         return redirect()->back()
            ->withErrors($validator)
            ->withInput();
      }

      $category = Category::create(['name' => $request->name]);

      if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
            $path = $image->store('categories', 's3');
            $url = Storage::disk('s3')->url($path);

            $category->images()->create([
               'url' => $url,
               'type' => 'public',
            ]);
         }
      }

      return redirect()->route('admin.categories.index')
         ->with('success', 'Category created successfully!');
   }

   public function edit(Category $category)
   {
      return view('admin.categories.edit', compact('category'));
   }

   public function update(Request $request, Category $category)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'sometimes|required|string|max:255',
         'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB
      ]);

      if ($validator->fails()) {
         return redirect()->back()
            ->withErrors($validator)
            ->withInput();
      }

      if ($request->has('name')) {
         $category->update(['name' => $request->name]);
      }

      if ($request->hasFile('image')) {
         if ($category->images()->exists()) {
            $oldImage = $category->images()->first();
            Storage::disk('s3')->delete($oldImage->url);
            $oldImage->delete();
         }

         $path = $request->file('image')->store('categories', 's3');
         $url = Storage::disk('s3')->url($path);

         $category->images()->create([
            'url' => $url,
            'type' => 'public',
         ]);
      }

      return redirect()->route('admin.categories.index')
         ->with('success', 'Category updated successfully!');
   }

   public function destroy(Category $category)
   {
      $category->delete();

      return redirect()->route('admin.categories.index')
         ->with('success', 'Category deleted successfully!');
   }
}