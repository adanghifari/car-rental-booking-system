<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                'unique:users,username',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Username tidak boleh menggunakan format email.');
                    }
                },
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'username' => Str::lower($validated['username']),
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_CUSTOMER,
        ]);

        return ApiResponse::created([
            'user' => $user,
            'redirect_to' => route('login', array_filter([
                'redirect' => $this->sanitizeRedirectPath($request->input('redirect')),
            ])),
        ], 'Registration successful.');
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        $login = Str::lower(trim($validated['login']));
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$login])
            ->orWhereRaw('LOWER(username) = ?', [$login])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            UserLog::log($user, 'Gagal Login (Password)', 'failed', $request, $login);
            return ApiResponse::unauthorized('Invalid credentials.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $plainTextToken = $user->createToken($this->tokenName($request))->plainTextToken;

        UserLog::log($user, 'Login Member', 'success', $request);

        return $this->withAccessTokenCookie(
            ApiResponse::success([
                'user' => $user,
                'redirect_to' => $this->resolveRedirectTarget($request, $user),
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

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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

    private function resolveRedirectTarget(Request $request, User $user): string
    {
        if ($user->role === User::ROLE_ADMIN) {
            return route('dashboard');
        }

        $redirect = $this->sanitizeRedirectPath($request->input('redirect'));

        if ($redirect !== null) {
            return url($redirect);
        }

        return route('frontliner');
    }

    private function sanitizeRedirectPath(mixed $redirect): ?string
    {
        if (! is_string($redirect)) {
            return null;
        }

        $redirect = trim($redirect);

        if ($redirect === '' || ! str_starts_with($redirect, '/')) {
            return null;
        }

        if (str_starts_with($redirect, '//')) {
            return null;
        }

        return $redirect;
    }
}
