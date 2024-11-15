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
    return Socialite::driver('google')->stateless()->redirect();
  }

  /**
   * Gérer le retour de Google.
   */
  public function handleGoogleCallback()
  {
    try {
      // Récupérer les informations de l'utilisateur Google
      $googleUser = Socialite::driver('google')->stateless()->user();

      if (!$googleUser || !$googleUser->getEmail()) {
        return response()->json(['error' => 'Invalid Google token or no email returned'], 401);
      }

      // Créer ou trouver l'utilisateur dans la base de données
      $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
          'name' => $googleUser->getName(),
          'password' => bcrypt(uniqid()), // Mot de passe temporaire
        ]
      );

      // Connecter l'utilisateur via Laravel Auth (session)
      Auth::login($user);

      // Rediriger l'utilisateur vers l'application frontend
      return redirect(env('FRONTEND_URL'));
    } catch (\Exception $e) {
      return response()->json(['error' => 'Failed to authenticate with Google'], 500);
    }
  }
}