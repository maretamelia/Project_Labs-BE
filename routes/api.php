<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ================= REGISTER / LOGIN / PASSWORD =================
Route::post('/register', [UserAuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('api.login');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.forgot-password');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('api.reset-password');

// ================= LOGOUT =================
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout');

// ================= Admin =================

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::post('/peminjaman/{peminjaman}/approve', [AdminController::class, 'approvePeminjaman']);
    Route::post('/peminjaman/{peminjaman}/reject', [AdminController::class, 'rejectPeminjaman']);
});

 // API CRUD (jika diperlukan)
 Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/api/barang', [\App\Http\Controllers\Admin\BarangController::class, 'apiIndex']);
    Route::get('/api/barang/{barang}', [\App\Http\Controllers\Admin\BarangController::class, 'apiShow']);
    Route::post('/api/barang', [\App\Http\Controllers\Admin\BarangController::class, 'apiStore']);
    Route::put('/api/barang/{barang}', [\App\Http\Controllers\Admin\BarangController::class, 'apiUpdate']);
    Route::delete('/api/barang/{barang}', [\App\Http\Controllers\Admin\BarangController::class, 'apiDestroy']);
});



// ================= USER PEMINJAMAN =================
Route::middleware('auth:sanctum')->prefix('user')->group(function () {

    Route::get('/peminjaman', [PeminjamanUserController::class, 'apiIndex'])->name('api.user.peminjaman.index');
    Route::get('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiShow'])->name('api.user.peminjaman.show');
    Route::post('/peminjaman', [PeminjamanUserController::class, 'apiStore'])->name('api.user.peminjaman.store');
    Route::put('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiUpdate'])->name('api.user.peminjaman.update');
    Route::delete('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiDestroy'])->name('api.user.peminjaman.destroy');
});
