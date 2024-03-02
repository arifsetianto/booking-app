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

Route::middleware(['auth.admin'])->group(function () {
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

    Route::view('canceled-orders', 'pages/orders/list-canceled')
         ->name('order.list-canceled');
});

Route::view('orders', 'pages/orders/list')
     ->middleware(['auth', 'verified'])
     ->name('order.list');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('complete-profile', 'complete-profile')
     ->middleware(['auth', 'verified'])
     ->name('profile.complete');

require __DIR__.'/auth.php';
