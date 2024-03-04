<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::view('/', 'welcome')->name('welcome');

Route::middleware(['auth.admin', 'verified', 'roles.has:admin'])->group(function () {
    Route::view('dashboard', 'dashboard')
         ->name('dashboard');

    Route::view('batches', 'pages/batches/list')
         ->name('batch.list');

    Route::view('batches/create', 'pages/batches/create')
         ->name('batch.create');

    Route::view('batches/{batch}/edit', 'pages/batches/edit')
         ->name('batch.edit');

    Route::view('incoming-orders', 'pages/orders/list-incoming')
         ->name('order.list-incoming');

    Route::view('complete-orders', 'pages/orders/list-completed')
         ->name('order.list-complete');

    Route::view('archive-orders', 'pages/orders/list-archive')
         ->name('order.list-archive');

    Route::view('orders/{order}/verify', 'pages/orders/verify')
         ->name('order.verify');

    Route::view('orders/{order}/complete', 'pages/orders/complete')
         ->name('order.complete');

    Route::view('orders/{order}/archive', 'pages/orders/archive')
         ->name('order.archive');
});

Route::middleware(['auth', 'verified', 'roles.has:customer'])->group(function () {
    Route::view('orders', 'pages/orders/list-by-user')
         ->name('order.list');

    Route::view('complete-profile', 'complete-profile')
         ->name('profile.complete');
});

Route::view('profile', 'profile')
    ->middleware(['auth', 'roles.has:root,customer'])
    ->name('profile');

require __DIR__.'/auth.php';
