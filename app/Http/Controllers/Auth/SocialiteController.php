<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        $accessToken = $request->input('access_token');

        try {
            // Utilisez Socialite pour récupérer les informations de l'utilisateur via le token
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($accessToken);

            // Vérifiez si Google a bien renvoyé un utilisateur
            if (!$googleUser || !$googleUser->getEmail()) {
                \Log::error('Google OAuth error: No email returned or invalid token.');
                return response()->json(['error' => 'Invalid Google token or no email returned'], 401);
            }

            \Log::info('Google User: ', ['user' => $googleUser]);

            // Créez ou trouvez l'utilisateur dans la base de données
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Mot de passe temporaire
                ]
            );

            // Connectez l'utilisateur et générez un bearer token Laravel
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            \Log::error('Failed to authenticate with Google: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to authenticate with Google'], 500);
        }
    }
}