<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\MidtransController;

Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
Route::post('cart/add-ajax/{product}', [CartController::class, 'addAjax'])->name('cart.add.ajax');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/products', [ShopController::class, 'products'])->name('shop.products');

Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
// Keep the previous detail URL available without shadowing the current route name.
Route::get('/orders/{order}', [UserOrderController::class, 'show'])
    ->middleware('auth')
    ->name('user.order.legacy');

Route::post('/pesanan/{order}/selesai', [UserOrderController::class, 'complete'])->name('user.order.complete');

// Rute untuk Autentikasi
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk Pengguna yang sudah Login
Route::middleware(['auth'])->group(function () {
    Route::get('/pesanan-saya', [UserOrderController::class, 'index'])->name('user.orders');
    Route::get('/riwayat-pesanan', [UserOrderController::class, 'history'])->name('user.history');
    Route::get('/pesanan/{order}', [UserOrderController::class, 'show'])->name('user.order.show');
    Route::post('/pesanan/{order}/bayar', [UserOrderController::class, 'pay'])->name('user.order.pay');
    Route::post('/pesanan/{order}/selesai', [UserOrderController::class, 'complete'])->name('user.order.complete');
    Route::get('/invoice/{order}/download', [UserOrderController::class, 'downloadInvoice'])->name('user.invoice.download');
});


Route::get('/', function () {
    return redirect('/shop');
});
