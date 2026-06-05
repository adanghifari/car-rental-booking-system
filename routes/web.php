<?php

use App\Http\Controllers\BackofficeController;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    return view('frontliner.pages.beranda-non-login');
})->middleware('token.cookie')->name('home');

Route::get('/beranda', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    return view('frontliner.pages.beranda-non-login');
})->middleware('token.cookie')->name('beranda');

Route::get('/frontliner', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    return view('frontliner.pages.beranda-login', [
        'user' => $user,
    ]);
})->middleware('auth')->name('frontliner');

Route::get('/booking/start', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    return redirect()->route('login', [
        'redirect' => '/frontliner',
    ]);
})->middleware('token.cookie')->name('booking.start');

Route::get('/beranda-login', function () {
    return redirect()->route('frontliner');
})->name('beranda.login');

Route::get('/dashboard', [BackofficeController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::get('/dashboard/users', [BackofficeController::class, 'users'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.users');

Route::get('/dashboard/cars', [BackofficeController::class, 'cars'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars');

Route::post('/dashboard/cars', [BackofficeController::class, 'storeCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.store');

Route::put('/dashboard/cars/{car}', [BackofficeController::class, 'updateCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.update');

Route::delete('/dashboard/cars/{car}', [BackofficeController::class, 'deleteCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.destroy');

Route::post('/logout', function (Request $request): RedirectResponse {
    $user = $request->user();

    if ($user?->currentAccessToken()) {
        $user->currentAccessToken()->delete();
    }

    auth()->guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->withCookie(cookie()->forget('access_token'));
})->middleware('auth')->name('logout');

Route::get('/beranda-non-login', function () {
    return view('frontliner.pages.beranda-non-login');
})->name('beranda.non-login');

Route::view('/welcome', 'welcome');
Route::get('/login', function (Request $request) {
    $user = $request->user();
    $redirect = $request->query('redirect');

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }

        return redirect()->route('frontliner');
    }

    return view('frontliner.auth.login');
})->name('login');

Route::get('/register', function (Request $request) {
    $user = $request->user();
    $redirect = $request->query('redirect');

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }

        return redirect()->route('frontliner');
    }

    return view('frontliner.auth.register');
})->name('register');
Route::view('/search-result', 'frontliner.pages.search-result')->name('search-result');
Route::view('/car-detail', 'frontliner.pages.car-detail')->name('car-detail');
