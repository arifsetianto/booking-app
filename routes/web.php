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

Route::view('incoming-orders', 'app/orders/list-incoming')
     ->middleware(['auth', 'verified'])
     ->name('order.list-incoming');

Route::view('complete-orders', 'app/orders/list-complete')
     ->middleware(['auth', 'verified'])
     ->name('order.list-complete');

Route::view('canceled-orders', 'app/orders/list-canceled')
     ->middleware(['auth', 'verified'])
     ->name('order.list-canceled');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
