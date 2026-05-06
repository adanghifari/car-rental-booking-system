<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_CUSTOMER,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::created([
            'user' => $user,
            'access_token' => $token,
        ], 'Registration successful.');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::whereEmail($validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return ApiResponse::unauthorized('Invalid credentials.');
        }

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'access_token' => $token,
        ], 'Login successful.');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return ApiResponse::success(null, 'Logout successful.');
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $user->tokens()->where('id', $currentToken->id)->delete();
        }

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'access_token' => $token,
        ], 'Token refreshed.');
    }
}