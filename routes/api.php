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
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::middleware('auth:sanctum')->post('/logout', [
    AuthenticatedSessionController::class,
    'destroy'
]);

/* =========================
| USER (SESUI WEB ROUTE)
========================= */
Route::middleware('auth:sanctum')
    ->prefix('user')
    ->group(function () {

        // USER - BARANG (VIEW ONLY)
        Route::get('/barang', [BarangUserController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangUserController::class, 'apiShow']);

        // USER - PEMINJAMAN
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
| ADMIN (SESUI WEB ROUTE)
========================= */
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        // ADMIN - KATEGORI
        Route::get('/kategori', [KategoriController::class, 'index']);          // list
        Route::post('/kategori', [KategoriController::class, 'store']);         // tambah
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update']); // update
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy']); // hapus
        // ADMIN - BARANG
        Route::get('/barang', [BarangController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangController::class, 'apiShow']);
        Route::post('/barang', [BarangController::class, 'apiStore']);
        Route::put('/barang/{barang}', [BarangController::class, 'apiUpdate']);
        Route::delete('/barang/{barang}', [BarangController::class, 'apiDestroy']);

        // ADMIN - PEMINJAMAN
        Route::get('/peminjaman', [PeminjamanAdminController::class, 'apiDaftar']);
        Route::get('/peminjaman/riwayat', [PeminjamanAdminController::class, 'apiRiwayat']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanAdminController::class, 'apiShow']);

        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanAdminController::class, 'apiApprove']);
        Route::post('/peminjaman/{peminjaman}/reject', [PeminjamanAdminController::class, 'apiReject']);
        Route::post('/peminjaman/{peminjaman}/return', [PeminjamanAdminController::class, 'apiReturn']);
    });
