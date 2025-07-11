<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::resource('table', ProductController::class);
Route::post('/checkout/{tableNo}', [ProductController::class, 'showDetail'])->name('checkout');
Route::get('midtrans/callback', [ProductController::class, 'callback']);
