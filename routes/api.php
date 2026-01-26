<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\User\BarangUserController;
use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\PeminjamanAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/* 🔽 ROUTE ADMIN DI SINI */

Route::prefix('admin')->group(function () {
    // KATEGORI (CRUD API)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::get('/kategori/{kategori}', [KategoriController::class, 'show']);
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy']);

    // BARANG (CRUD API)
    Route::get('/barang', [BarangController::class, 'apiIndex']); // semua barang
    Route::get('/barang/{barang}', [BarangController::class, 'apiShow']); // detail barang
    Route::post('/barang', [BarangController::class, 'apiStore']); // tambah barang
    Route::put('/barang/{barang}', [BarangController::class, 'apiUpdate']); // update barang
    Route::delete('/barang/{barang}', [BarangController::class, 'apiDestroy']); // hapus barang
});



// ROUTE USER 

    Route::prefix('user')->middleware(['auth'])->group(function () {
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->name('api.password.store');
    Route::get('barangs', [BarangUserController::class, 'index'])->name('user.barangs.index');
    Route::get('barangs/{barang}', [BarangUserController::class, 'show'])->name('user.barangs.show');
});

    // TEST API tanpa login
Route::post('/peminjaman-test', [PeminjamanUserController::class, 'store']);

// Jika mau pakai login user via Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/peminjaman', [PeminjamanUserController::class, 'store'])
        ->name('api.peminjaman.store');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('api.password.store');
});

require __DIR__.'/auth.php';