<?php

use App\Http\Controllers\Auth\LoginEmailController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::domain('book.' . config('app.url'))->middleware('guest')->group(function () {
    Volt::route('/', 'pages.auth.on-boarding')
        ->name('on-boarding');

    Volt::route('login-email', 'pages.auth.login-email')
        ->name('email.login');

    Volt::route('email-link-verification', 'pages.auth.email-link-verification')
        ->name('email.link.verification');

    Route::get(
        'login/{email}',
        LoginEmailController::class,
    )->middleware('signed')->name('login.email.store');
});

Route::domain('sys.' . config('app.url'))->middleware('guest')->group(function () {
    //Volt::route('register', 'pages.auth.register')
    //    ->name('register');

    Volt::route('/', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
