<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   public function register(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:6',
         'pin' => 'required|digits:6',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }

      // Create the user
      $user = User::create([
         'name' => $request->name,
         'email' => $request->email,
         'password' => bcrypt($request->password),
         'pin' => bcrypt($request->password),
      ]);

      // Generate a new API token for the user
      $token = $user->createToken('API Token')->plainTextToken;

      return response()->json([
         'token' => $token,
         'message' => 'Register Success!'
      ], 201);
   }

   public function login(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'email' => 'required|string|email|max:255',
         'password' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }

      // Attempt to authenticate the user
      if (!Auth::attempt($request->only('email', 'password'))) {
         return response()->json(['message' => 'Invalid credentials'], 401);
      }

      // Get the authenticated user
      $user = Auth::user();
      $userData = User::find($user->id);

      // Generate a new API token for the user
      $token = $user->createToken('API Token')->plainTextToken;

      return response()->json([
         'userData' => $userData,
         'token' => $token,
         'message' => 'Login Success!'
      ], 200);
   }

   public function logout(Request $request)
   {
      // Revoke the user's API token
      $request->user()->currentAccessToken()->delete();

      return response()->json(['message' => 'Logged out'], 200);
   }
}
