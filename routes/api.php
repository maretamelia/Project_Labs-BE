<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\User\BarangUserController;
use App\Http\Controllers\User\DashboardUserController;

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\PeminjamanAdminController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::middleware('auth:sanctum')->post(
    '/logout',
    [AuthenticatedSessionController::class, 'apiLogout']
);

/*
|--------------------------------------------------------------------------
| USER ROUTES (ROLE: USER)
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->middleware(['auth:sanctum', 'role:user'])
    ->group(function () {
    
    Route::get('/dashboard', [DashboardUserController::class, 'index']);

        // PROFILE USER
        Route::get('/profile', function (Request $request) {
            return response()->json($request->user());
        });

        // BARANG (VIEW ONLY)
        Route::get('/barang', [BarangUserController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangUserController::class, 'apiShow']);

        // PEMINJAMAN USER
        Route::prefix('pinjaman')->group(function () {
            Route::get('/', [PeminjamanUserController::class, 'apiIndex']);
            Route::get('/riwayat', [PeminjamanUserController::class, 'apiRiwayat']);
            Route::post('/', [PeminjamanUserController::class, 'apiStore']);
            Route::get('/{peminjaman}', [PeminjamanUserController::class, 'apiShow']);
            Route::put('/{peminjaman}', [PeminjamanUserController::class, 'apiUpdate']);
            Route::delete('/{peminjaman}', [PeminjamanUserController::class, 'apiDestroy']);
            Route::post('/{peminjaman}/return', [PeminjamanUserController::class, 'apiReturn']);
        });
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE: ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {


    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard-stats', [DashboardController::class, 'dashboardStats']);

        // PROFILE ADMIN
        Route::get('/profile', function (Request $request) {
            return response()->json($request->user());
        });

        // KATEGORI
        Route::get('/kategori', [KategoriController::class, 'index']);
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        // BARANG
        Route::get('/barang', [BarangController::class, 'apiIndex']);
        Route::get('/barang/{barang}', [BarangController::class, 'apiShow']);
        Route::post('/barang', [BarangController::class, 'apiStore']);
        Route::put('/barang/{barang}', [BarangController::class, 'apiUpdate']);
        Route::delete('/barang/{barang}', [BarangController::class, 'apiDestroy']);

        // PEMINJAMAN ADMIN
        Route::get('/peminjaman', [PeminjamanAdminController::class, 'apiIndex']);
        Route::get('/peminjaman/riwayat', [PeminjamanAdminController::class, 'apiRiwayat']);
        Route::get('/peminjaman/{id}', [PeminjamanAdminController::class, 'apiShow']);
        Route::post('/peminjaman/{id}/approve', [PeminjamanAdminController::class, 'apiApprove']);
        Route::post('/peminjaman/{id}/reject', [PeminjamanAdminController::class, 'apiReject']);
    });
