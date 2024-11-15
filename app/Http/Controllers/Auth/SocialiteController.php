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
      // Récupérer les informations de l'utilisateur Google
      $googleUser = Socialite::driver('google')->stateless()->user();

      if (!$googleUser || !$googleUser->getEmail()) {
        return redirect(env('FRONTEND_URL') . '?auth=error');
      }

      // Créer ou mettre à jour l'utilisateur dans la base de données
      $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
          'name' => $googleUser->getName(),
          'google_id' => $googleUser->getId(),
          'password' => bcrypt(uniqid()), // Mot de passe temporaire
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