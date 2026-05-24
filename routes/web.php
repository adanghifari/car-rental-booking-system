<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return view('beranda-login');
    }
    return view('beranda-non-login');
})->name('home');

Route::get('/beranda', function () {
    if (auth()->check()) {
        return view('beranda-login');
    }
    return view('beranda-non-login');
})->name('beranda');

Route::get('/beranda-login', function () {
    return view('beranda-login');
})->middleware('auth')->name('beranda.login');

Route::get('/beranda-non-login', function () {
    return view('beranda-non-login');
})->name('beranda.non-login');

Route::view('/welcome', 'welcome');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
