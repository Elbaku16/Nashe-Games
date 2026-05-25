<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Nashe Games
|--------------------------------------------------------------------------
| Auth + Store + Library + Cart (con datos desde CheapShark API)
*/

// ===== Auth (públicas) =====
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');

// ===== Rutas autenticadas =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Store
    Route::get('/store', [StoreController::class, 'index'])->name('store');
    Route::get('/store/game/{dealID}', [StoreController::class, 'show'])->where('dealID', '.*')->name('store.game');

    // Library
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::delete('/library/{libraryItem}', [LibraryController::class, 'uninstall'])->name('library.uninstall');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});
