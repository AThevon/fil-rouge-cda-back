<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Controllers\Controller;

class SocialiteController extends Controller
{
  /**
   * Redirection vers Google.
   */
  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }

  /**
   * Gérer le retour de Google.
   */
  // SocialiteController.php
  public function handleGoogleCallback()
  {
    try {
      $googleUser = Socialite::driver('google')->stateless()->user();

      if (!$googleUser || !$googleUser->getEmail()) {
        return redirect(env('FRONTEND_URL') . '?auth=error');
      }

      $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
          'name' => $googleUser->getName(),
          'google_id' => $googleUser->getId(),
          'password' => bcrypt(uniqid()),
        ]
      );

      Auth::login($user);
      session()->regenerate();

      return redirect(env('FRONTEND_URL'));
    } catch (\Exception $e) {
      return redirect(env('FRONTEND_URL') . '?auth=error');
    }
  }
}