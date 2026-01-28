<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserAuthController; // optional, kalau mau register API
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

// ================= USER PEMINJAMAN =================
Route::middleware('auth:sanctum')->prefix('user')->group(function () {

    Route::get('/peminjaman', [PeminjamanUserController::class, 'apiIndex'])->name('api.user.peminjaman.index');
    Route::get('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiShow'])->name('api.user.peminjaman.show');
    Route::post('/peminjaman', [PeminjamanUserController::class, 'apiStore'])->name('api.user.peminjaman.store');
    Route::put('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiUpdate'])->name('api.user.peminjaman.update');
    Route::delete('/peminjaman/{peminjaman}', [PeminjamanUserController::class, 'apiDestroy'])->name('api.user.peminjaman.destroy');
});
