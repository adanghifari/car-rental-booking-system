<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\UserReportController;
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

        Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                'throttle:auth-forgot-password',
            ]);

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
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

        // User Report submission
        Route::post('/report', [UserReportController::class, 'store']);
        Route::post('/reports', [UserReportController::class, 'store']);
    });

    Route::middleware(['token.cookie', 'auth:sanctum', 'admin'])->group(function () {
        Route::get('/rentals/count', [RentalController::class, 'count']);
        Route::get('/rentals', [RentalController::class, 'index']);
        Route::get('/rentals/{rental}', [RentalController::class, 'show']);
        Route::put('/rentals/{rental}', [RentalController::class, 'update']);
        Route::delete('/rentals/{rental}', [RentalController::class, 'destroy']);

        // User activity logs
        Route::get('/log', [UserLogController::class, 'index']);
        Route::get('/logs', [UserLogController::class, 'index']);

        // User trouble reports
        Route::get('/report', [UserReportController::class, 'index']);
        Route::get('/reports', [UserReportController::class, 'index']);
    });

    Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
});
