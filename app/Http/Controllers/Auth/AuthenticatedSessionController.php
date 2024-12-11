<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
  /**
   * Handle an incoming authentication request.
   */
  public function store(LoginRequest $request): JsonResponse
  {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
    }

    Auth::login($user);

    return response()->json(['user' => $user], 200);
  }

  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $cookie = cookie()->forget('XSRF-TOKEN');

    return response()->json(['message' => 'Logged out successfully'], 200)->withCookie($cookie);
  }
}