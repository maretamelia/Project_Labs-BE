<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\User\BarangUserController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\PeminjamanAdminController;
use App\Http\Controllers\Admin\KategoriController;

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/

/* =========================
| AUTH
========================= */
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

// Logout protected
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'apiLogout']);

/* =========================
| USER ROUTES
========================= */
Route::prefix('user')
    ->middleware('auth:sanctum')
    ->group(function () {

        // PROFIL
        Route::get('/profile', function (\Illuminate\Http\Request $request) {
            return response()->json($request->user());
        });

        // BARANG (VIEW ONLY)
        Route::get('/barang', [BarangUserController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangUserController::class, 'apiShow']);

        // PEMINJAMAN
        Route::prefix('peminjaman')->group(function () {
            Route::get('/', [PeminjamanUserController::class, 'apiIndex']);
            Route::post('/', [PeminjamanUserController::class, 'apiStore']);
            Route::get('/{peminjaman}', [PeminjamanUserController::class, 'apiShow']);
            Route::put('/{peminjaman}', [PeminjamanUserController::class, 'apiUpdate']);
            Route::delete('/{peminjaman}', [PeminjamanUserController::class, 'apiDestroy']);
            Route::post('/{peminjaman}/return', [PeminjamanUserController::class, 'apiReturn']);
        });
    });

/* =========================
| ADMIN ROUTES
========================= */
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {

        // PROFIL
        Route::get('/profile', function (\Illuminate\Http\Request $request) {
            return response()->json($request->user());
        });

        // KATEGORI
        Route::get('/kategori', [KategoriController::class, 'index']);          // list
        Route::post('/kategori', [KategoriController::class, 'store']);         // tambah
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update']); // update
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy']); // hapus

        // BARANG
        Route::get('/barang', [BarangController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangController::class, 'apiShow']);
        Route::post('/barang', [BarangController::class, 'apiStore']);
        Route::put('/barang/{barang}', [BarangController::class, 'apiUpdate']);
        Route::delete('/barang/{barang}', [BarangController::class, 'apiDestroy']);

        // PEMINJAMAN
        Route::get('/peminjaman', [PeminjamanAdminController::class, 'apiIndex']);
        Route::get('/peminjaman/riwayat', [PeminjamanAdminController::class, 'apiRiwayat']);
        Route::get('/peminjaman/{id}', [PeminjamanAdminController::class, 'apiShow']);
        Route::post('/peminjaman/{id}/approve', [PeminjamanAdminController::class, 'apiApprove']);
        Route::post('/peminjaman/{id}/reject', [PeminjamanAdminController::class, 'apiReject']);
    });
