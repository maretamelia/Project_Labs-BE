<?php

// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\Admin\KategoriController;
// use App\Http\Controllers\User\BarangUserController;
 use App\Http\Controllers\User\PeminjamanUserController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\BarangController;
// use App\Http\Controllers\PeminjamanController;




// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });
// /* 🔽 ROUTE ADMIN DI SINI */

//     Route::prefix('admin')->group(function() {
//     // kategori
//     Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
//     Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
//     Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
//     Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
//     Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
//     Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
//     // Barang
//     Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
//     Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
//     Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
//     Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
//     Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
//     Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
//     Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
// });

// // ROUTE USER 

//     Route::prefix('user')->middleware(['auth'])->group(function () {
//     Route::get('barangs', [BarangUserController::class, 'index'])->name('user.barangs.index');
//     Route::get('barangs/{barang}', [BarangUserController::class, 'show'])->name('user.barangs.show');
// });

 Route::prefix('user')->middleware(['auth'])->group(function () {
    // Tampilkan form
    Route::get('peminjaman/create', [PeminjamanUserController::class, 'create'])
        ->name('user.peminjaman.create');

    // Simpan data peminjaman
    Route::post('peminjaman', [PeminjamanUserController::class, 'store'])
        ->name('user.peminjaman.store');
//     Route::post('peminjaman', [PeminjamanController::class, 'store'])
//         ->name('user.peminjaman.store');
// });

// require __DIR__.'/auth.php';
    });    