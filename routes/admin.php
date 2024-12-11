<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;

Route::prefix('admin')
   ->middleware(['auth:sanctum', 'admin'])
   ->group(function () {
      // Admin Product Management
      Route::prefix('products')->group(function () {
         Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
         Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
         Route::put('/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
         Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
      });

      // Admin Category Management
      Route::prefix('categories')->group(function () {
         Route::get('/', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
         Route::post('/', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
         Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
         Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
      });

      // Admin Orders Management
      Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
   });
