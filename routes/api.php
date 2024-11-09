<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Stripe\StripeController;
use App\Http\Controllers\Stripe\StripeWebhookController;

// GUEST ROUTES (Accessible without authentication)
Route::middleware(['guest'])->group(function () {
  // Registration and Login
  Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
  Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

  // Password Reset
  Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
  Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// SOCIAL AUTHENTICATION (Google)
Route::get('auth/google/redirect', [SocialiteController::class, 'redirectToGoogle']);
Route::post('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

// AUTHENTICATED USER ROUTES (Require auth:sanctum middleware)
Route::middleware(['auth:sanctum'])->group(function () {
  // User Account Management
  Route::get('/user', [RegisteredUserController::class, 'show'])->name('user.show');
  Route::put('/user', [RegisteredUserController::class, 'update'])->name('user.update');
  Route::put('/user/password', [RegisteredUserController::class, 'updatePassword'])->name('user.updatePassword');
  Route::delete('/user', [RegisteredUserController::class, 'destroy'])->name('user.destroy');

  // Email Verification
  Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
  Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

  // Logout
  Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// PUBLIC PRODUCTS AND CATEGORIES ROUTES
// These routes do not require authentication
Route::prefix('products')->group(function () {
  Route::get('/', [ProductController::class, 'index'])->name('products.index');
  Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::prefix('categories')->group(function () {
  Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
  Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
});

// ADMIN-ONLY ROUTES (auth:sanctum and admin middleware required)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
  // Admin Product Management
  Route::prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
  });

  // Admin Category Management
  Route::prefix('categories')->group(function () {
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
  });

  // Admin Orders Management
  Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

// AUTHENTICATED USER ROUTES
Route::middleware(['auth:sanctum'])->group(function () {
  // User Product Actions
  Route::post('/products/{product}/toggle-vote', [ProductController::class, 'toggleVote'])->name('products.toggleVote');

  // User Orders Management
  Route::prefix('orders')->group(function () {
    Route::get('/user', [OrderController::class, 'indexByUser'])->name('orders.indexByUser');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
  });

  // User Custom Requests Management
  Route::prefix('custom-requests')->group(function () {
    Route::get('/', [\App\Http\Controllers\CustomRequestController::class, 'index'])->name('custom-requests.index');
    Route::post('/', [\App\Http\Controllers\CustomRequestController::class, 'store'])->name('custom-requests.store');
    Route::get('/{customRequest}', [\App\Http\Controllers\CustomRequestController::class, 'show'])->name('custom-requests.show');
    Route::put('/{customRequest}', [\App\Http\Controllers\CustomRequestController::class, 'update'])->name('custom-requests.update');
    Route::delete('/{customRequest}', [\App\Http\Controllers\CustomRequestController::class, 'destroy'])->name('custom-requests.destroy');
  });

  // STRIPE PAYMENT ROUTE
  Route::post('/stripe/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
  Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
});
