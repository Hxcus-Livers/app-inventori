<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root URL to login/register
Route::get('/', function () {
    return redirect()->route('login');
});

// Tambahkan middleware auth untuk semua route yang membutuhkan autentikasi
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Redirect /items ke items.index
    Route::get('/items', function () {
        return redirect()->route('items.index');
    });

    // Resource routes
    Route::resource('items', ItemController::class);
    Route::resource('categories', CategoryController::class);

    // Checkout routes
    Route::get('checkouts', [CheckoutController::class, 'index'])->name('checkouts.index');
    Route::post('checkouts', [CheckoutController::class, 'store'])->name('checkouts.store');

    // History route
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';