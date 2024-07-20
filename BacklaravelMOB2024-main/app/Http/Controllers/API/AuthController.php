<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
   /* public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }*/


     /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Retrieve the user by email
        $user = User::where('email', $attributes['email'])->first();
    
        // Check if user exists
        if (!$user) {
            return response(['message' => "User not found"], 404);
        }
    
        // Verify the password
        if (!Hash::check($attributes['password'], $user->password)) {
            return response(['message' => "Invalid credentials"], 403);
        }
    
        // Authenticate the user
        Auth::login($user);
    
        // Retrieve the authenticated user
        $user = Auth::user();
    
        // Create the token
        $token = $user->createToken('token')->plainTextToken;
    
        return response([
            'user' => $user->toArray(),
            'token' => $token,
        ], 200);
    }
    

 /**
     * Handle user logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
