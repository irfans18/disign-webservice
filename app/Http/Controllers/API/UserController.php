<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\DeviceController;

class UserController extends Controller
{
   public function register(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'username' => 'required|string|min:4|max:20|unique:users',
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:6',
         'pin' => 'required|digits:6',
         'hwid' => 'required|string',
         'device_name' => 'required|string',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }

      // Create the user
      $user = User::create([
         'username' => $request->username,
         'name' => $request->name,
         'email' => $request->email,
         'password' => bcrypt($request->password),
         'pin' => bcrypt($request->password),
      ]);

      $deviceController = app(DeviceController::class);
      $device = $deviceController->store($user->id, $request->hwid, $request->device_name);

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
         // 'email' => 'required|string|email|max:255',
         'username' => 'required|string|max:20',
         'password' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }

      // Attempt to authenticate the user
      if (!Auth::attempt($request->only('username', 'password'))) {
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
