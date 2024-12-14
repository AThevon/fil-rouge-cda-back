<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomRequestController;
use App\Http\Controllers\ContactController;


// GUEST ROUTES (Accessible without authentication)
Route::middleware(['guest'])
   ->group(function () {

      Route::post('/register', [RegisteredUserController::class, 'store'])
         ->name('register')
         ->middleware('throttle:6,1');

      Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
         ->name('password.email');

      Route::post('/reset-password', [NewPasswordController::class, 'store'])
         ->name('password.store');
   });


// PUBLIC ROUTES
Route::prefix('products')->group(function () {
   Route::get('/', [ProductController::class, 'index'])->name('products.index');
   Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::get('/categories', [CategoryController::class, 'index'])
   ->name('categories.index');

Route::post('/contact', [ContactController::class, 'send'])
   ->middleware('throttle:6,1')
   ->name('contact.send');



// AUTHENTICATED USER ROUTES
Route::middleware(['auth:sanctum'])->group(function () {

   // User Account
   Route::prefix('user')->group(function () {
      Route::get('/', [RegisteredUserController::class, 'show'])->name('user.show');
      Route::put('/', [RegisteredUserController::class, 'update'])->name('user.update');
      Route::put('/password', [RegisteredUserController::class, 'updatePassword'])->name('user.updatePassword');
      Route::delete('/', [RegisteredUserController::class, 'destroy'])->name('user.destroy');
   });

   // Email Verification
   Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
      ->middleware(['signed', 'throttle:6,1'])
      ->name('verification.verify');

   Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
      ->middleware('throttle:6,1')
      ->name('verification.send');

   //Vote For Product
   Route::post('/products/{product}/toggle-vote', [ProductController::class, 'toggleVote'])
      ->name('products.toggleVote');

   // Custom Requests
   Route::prefix('custom-requests')->group(function () {
      Route::get('/', [CustomRequestController::class, 'index'])->name('custom-requests.index');
      Route::post('/', [CustomRequestController::class, 'store'])->name('custom-requests.store');
      Route::get('/{customRequest}', [CustomRequestController::class, 'show'])->name('custom-requests.show');
   });

   // User Orders
   Route::prefix('orders')->group(function () {
      Route::get('/', [OrderController::class, 'index'])->name('orders.index');
      Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
      Route::post('/', [OrderController::class, 'store'])->name('orders.store');
   });

   // User Payments
   Route::post('/payment', [PaymentController::class, 'store'])
      ->name('payment.store');
});
