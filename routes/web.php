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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('batches', 'pages/batches/list')
     ->middleware(['auth', 'verified'])
     ->name('batch.list');

Route::view('batches/create', 'pages/batches/create')
     ->middleware(['auth', 'verified'])
     ->name('batch.create');

Route::view('batches/{batch}/edit', 'pages/batches/edit')
     ->middleware(['auth', 'verified'])
     ->name('batch.edit');

Route::view('orders', 'pages/orders/list')
     ->middleware(['auth', 'verified'])
     ->name('order.list');

Route::view('incoming-orders', 'pages/orders/list-incoming')
     ->middleware(['auth', 'verified'])
     ->name('order.list-incoming');

Route::view('complete-orders', 'pages/orders/list-complete')
     ->middleware(['auth', 'verified'])
     ->name('order.list-complete');

Route::view('canceled-orders', 'pages/orders/list-canceled')
     ->middleware(['auth', 'verified'])
     ->name('order.list-canceled');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('complete-profile', 'complete-profile')
     ->middleware(['auth', 'verified'])
     ->name('profile.complete');

require __DIR__.'/auth.php';
