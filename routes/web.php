<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\User\BarangUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\PeminjamanAdminController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\KategoriController;


/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// HALAMAN AWAL
Route::get('/', function () {
    return view('welcome');
});

// DASHBOARD
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ================= REGISTER / LOGIN / PASSWORD =================
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// ================= LOGOUT =================
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ================= PROFILE =================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ================= USER ROUTES =================
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    
    // USER - BARANG (View Only)
    Route::get('/barang', [BarangUserController::class, 'index'])->name('barang.index');
    Route::get('/barang/{barang}', [BarangUserController::class, 'show'])->name('barang.show');
    
    // USER - PEMINJAMAN
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {

    Route::get('/', [PeminjamanUserController::class, 'index'])->name('index');

    // STEP 1
    Route::get('/create', [PeminjamanUserController::class, 'create'])->name('create');

    // STEP 2 (form isi data)
    Route::get('/form', [PeminjamanUserController::class, 'form'])->name('form');

    // STEP 3
    Route::post('/', [PeminjamanUserController::class, 'store'])->name('store');

    Route::get('/{peminjaman}/edit', [PeminjamanUserController::class, 'edit'])->name('edit');
    Route::put('/{peminjaman}', [PeminjamanUserController::class, 'update'])->name('update');
    Route::delete('/{peminjaman}', [PeminjamanUserController::class, 'destroy'])->name('destroy');
    Route::post('/{peminjaman}/return', [PeminjamanUserController::class, 'return'])->name('return');
    Route::get('/{peminjaman}', [PeminjamanUserController::class, 'show'])->name('show');

});
});

// ================= ADMIN ROUTES =================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    
// ADMIN - KATEGORI
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::get('/kategori/{kategori}', [KategoriController::class, 'show'])->name('kategori.show');
// ADMIN - BARANG
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    // ADMIN - PEMINJAMAN
    // Daftar & riwayat
    Route::get('/peminjaman', [PeminjamanAdminController::class, 'daftarPeminjaman'])->name('peminjaman.daftar');
    Route::get('/peminjaman/riwayat', [PeminjamanAdminController::class, 'riwayatPeminjaman'])->name('peminjaman.riwayat');
    // Action approve/reject/return
    Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanAdminController::class, 'approvePeminjaman'])->name('peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/reject', [PeminjamanAdminController::class, 'rejectPeminjaman'])->name('peminjaman.reject');
    Route::post('/peminjaman/{peminjaman}/return', [PeminjamanAdminController::class, 'returnPeminjaman'])->name('peminjaman.return');
    Route::get('/peminjaman/{peminjaman}', [PeminjamanAdminController::class, 'show'])->name('peminjaman.show');

});

require __DIR__.'/auth.php';