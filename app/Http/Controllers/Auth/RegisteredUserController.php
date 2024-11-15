<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */

  public function show()
  {
    $user = Auth::user();

    return response()->json(['user' => $user]);
  }

  public function store(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
      'password' => ['required', 'confirmed', Password::defaults()],
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->string('password')),
    ]);

    event(new Registered($user));

    // Connecter l'utilisateur via session
    Auth::login($user);

    return response()->json([
      'user' => $user,
      'message' => 'User registered and logged in successfully'
    ], 201);
  }

  public function update(Request $request): JsonResponse
  {
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|required|string|max:255',
      'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
      'password' => 'sometimes|confirmed|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    if ($request->has('name')) {
      $user->name = $request->name;
    }
    if ($request->has('email')) {
      $user->email = $request->email;
    }
    if ($request->has('password')) {
      $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json(['user' => $user, 'message' => 'User updated successfully']);
  }
  public function updatePassword(Request $request): JsonResponse
  {
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
      'current_password' => 'required',
      'password' => 'required|confirmed|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    if (!Hash::check($request->current_password, $user->password)) {
      return response()->json([
        'errors' => ['current_password' => ['The provided password does not match your current password.']]
      ], 422);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json(['message' => 'Password updated successfully']);
  }

  public function destroy($id)
  {
    $user = Auth::user();
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
  }
}
