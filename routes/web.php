<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\KategoriController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::get('/kategori', [KategoriController::class, 'index']);
});


