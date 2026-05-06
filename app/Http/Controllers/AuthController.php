<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_CUSTOMER,
        ]);

        $plainTextToken = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->withAccessTokenCookie(
            ApiResponse::created([
                'user' => $user,
            ], 'Registration successful.'),
            $plainTextToken,
            $request
        );
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return ApiResponse::unauthorized('Invalid credentials.');
        }

        $plainTextToken = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->withAccessTokenCookie(
            ApiResponse::success([
                'user' => $user,
            ], 'Login successful.'),
            $plainTextToken,
            $request
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        return ApiResponse::success(null, 'Logout successful.')->withoutCookie('access_token');
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        $plainTextToken = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->withAccessTokenCookie(
            ApiResponse::success(null, 'Token refreshed.'),
            $plainTextToken,
            $request
        );
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        return ApiResponse::success([
            'user' => $user,
        ], 'Authenticated user retrieved.');
    }

    private function withAccessTokenCookie(JsonResponse $response, string $plainTextToken, Request $request): JsonResponse
    {
        $minutes = 60 * 24 * 7;
        $secure = $request->isSecure() || app()->environment('production');

        return $response->cookie(
            'access_token',
            $plainTextToken,
            $minutes,
            '/',
            null,
            $secure,
            true,
            false,
            'lax'
        );
    }

    private function tokenName(Request $request): string
    {
        return 'web:'.($request->userAgent() ?: 'unknown');
    }
}
