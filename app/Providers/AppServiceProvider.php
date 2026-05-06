<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth-login', function (Request $request) {
            $email = (string) $request->input('email');
            $identifier = mb_strtolower($email).'|'.$request->ip();

            return [
                Limit::perMinute(5)->by($identifier),
                Limit::perMinute(30)->by($request->ip()),
            ];
        });

        RateLimiter::for('auth-register', function (Request $request) {
            return [Limit::perMinute(5)->by($request->ip())];
        });

        RateLimiter::for('auth-refresh', function (Request $request) {
            $userId = (string) optional($request->user())->id;
            $identifier = $userId !== '' ? 'user:'.$userId : 'ip:'.$request->ip();

            return [Limit::perMinute(20)->by($identifier)];
        });
    }
}
