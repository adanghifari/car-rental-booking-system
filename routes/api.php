<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                'throttle:auth-register',
            ]);
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                'throttle:auth-login',
            ]);

        Route::middleware(['token.cookie', 'auth:sanctum'])->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout'])
                ->middleware([
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                ]);
            Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('throttle:auth-refresh');
        });
    });

    Route::apiResource('car', CarController::class);

    Route::middleware(['token.cookie', 'auth:sanctum'])->group(function () {
        Route::post('/rentals', [RentalController::class, 'store']);
        Route::post('/rentals/{rental}/return', [RentalController::class, 'markReturned']);
        Route::post('/payment', [PaymentController::class, 'create']);
    });

    Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
});
