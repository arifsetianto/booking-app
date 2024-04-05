<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Shipping\PdfController;
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

Route::middleware(['auth.admin', 'verified', 'roles.has:admin'])->group(
    function () {
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

        Route::view('shipped-orders', 'pages/orders/list-shipped')
             ->name('order.list-shipped');

        Route::view('archive-orders', 'pages/orders/list-archive')
             ->name('order.list-archive');

        Route::view('invited-orders', 'pages/orders/list-invited')
             ->name('order.list-invited');

        Route::view('orders/{order}/verify', 'pages/orders/verify')
             ->name('order.verify');

        Route::view('orders/{order}/complete', 'pages/orders/complete')
             ->name('order.complete');

        Route::view('orders/{order}/shipped', 'pages/orders/shipped')
             ->name('order.shipped');

        Route::view('orders/{order}/archive', 'pages/orders/archive')
             ->name('order.archive');

        Route::view('orders/invite', 'pages/orders/invite')
             ->name('order.invite.create');

        Route::view('orders/invite-existing', 'pages/orders/invite-existing')
             ->name('order.invite-existing.create');

        Route::view('orders/{order}/invited', 'pages/orders/invited')
             ->name('order.invited');

        Route::view('profile', 'profile')
             ->name('profile');

        Route::get('orders/{order}/shipping-label/generate', [PdfController::class, 'generateLabel'])
             ->name('shipping.label.generate');

        Route::get('verify/resend', [TwoFactorController::class, 'resend'])->name('verify.resend');
        Route::resource('verify', TwoFactorController::class)->only(['index', 'store']);

        Route::get('orders/verified/export', [OrderController::class, 'verifiedExport'])->name(
            'orders.verified.export'
        );
    }
);

Route::view('orders/{order}/request-update', 'pages/orders/request-update')
     ->middleware(['signed', 'force.auth'])
     ->name('orders.request-update');

Route::view('orders/{order}/payment/force', 'pages/orders/payment')
     ->middleware(['force.auth'])
     ->name('orders.payment.force');

Route::view('orders/{order}/tracking-status/force', 'pages/orders/tracking-status')
     ->middleware(['force.auth'])
     ->name('orders.tracking.status.force');

Route::view('orders/{order}/confirm-invitation', 'pages/orders/confirm-invitation')
     ->middleware(['signed', 'force.auth'])
     ->name('orders.confirm-invitation');

Route::middleware(['auth', 'verified', 'roles.has:customer'])->group(
    function () {
        Route::view('home', 'pages/home')
             ->name('home');

        Route::view('book-order', 'pages/orders/book')
             ->middleware(['stock.check'])
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

        Route::view('orders/{order}/update-success', 'pages/orders/update-success')
             ->name('orders.update.success');

        Route::view('orders/{order}/update-error', 'pages/orders/update-error')
             ->name('orders.update.error');

        Route::view('orders', 'pages/orders/list-by-user')
             ->name('orders.list');

        Route::view('complete-profile', 'complete-profile')
             ->name('profile.complete');

        Route::view('customer-profile', 'customer-profile')
             ->name('profile.customer');

        Route::view('stock-unavailable', 'pages/batches/stock-unavailable')
             ->name('stock.unavailable');
    }
);

require __DIR__ . '/auth.php';
