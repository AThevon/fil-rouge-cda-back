<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Stripe\StripeWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
   return ['Laravel' => app()->version()];
})->name('home');

Route::get('/sanctum/csrf-cookie', function () {
   return response()->json(['message' => 'CSRF cookie set']);
});


Route::middleware(['web'])->group(function () {
   Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
   Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
   Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle']);
   Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
});


Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
   ->withoutMiddleware([VerifyCsrfToken::class]);
   

