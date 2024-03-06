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
    Route::view('home', 'pages/home')
         ->name('home');

    Route::view('book-order', 'pages/orders/book')
         ->name('orders.book');

    Route::view('orders/{order}/delivery', 'pages/orders/delivery')
         ->name('orders.delivery');

    Route::view('orders/{order}/edit', 'pages/orders/edit')
         ->name('orders.edit');

    Route::view('orders/{order}/payment', 'pages/orders/payment')
         ->name('orders.payment');

    Route::view('orders/{order}/payment-success', 'pages/orders/payment-success')
         ->name('orders.payment.success');

    Route::view('orders/{order}/tracking-status', 'pages/orders/tracking-status')
         ->name('orders.tracking.status');

    Route::view('orders/{order}/detail', 'pages/orders/detail')
         ->name('orders.detail');

    Route::view('orders', 'pages/orders/list-by-user')
         ->name('orders.list');

    Route::view('complete-profile', 'complete-profile')
         ->name('profile.complete');
});

Route::view('profile', 'profile')
    ->middleware(['auth', 'roles.has:root,customer'])
    ->name('profile');

require __DIR__.'/auth.php';
