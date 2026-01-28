<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\PeminjamanUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES (BLADE / HTML)
|--------------------------------------------------------------------------
*/

// HALAMAN AWAL
Route::get('/', function () {
    return view('welcome');
});

// DASHBOARD (hanya untuk user login + verified)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ================= REGISTER / LOGIN / PASSWORD =================
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Forgot password
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Reset password
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

    // ================= USER PEMINJAMAN =================
    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
            Route::get('/', [PeminjamanUserController::class, 'index'])->name('index');
            Route::get('/create', [PeminjamanUserController::class, 'create'])->name('create');
            Route::post('/', [PeminjamanUserController::class, 'store'])->name('store');
            Route::get('/{peminjaman}/edit', [PeminjamanUserController::class, 'edit'])->name('edit');
            Route::put('/{peminjaman}', [PeminjamanUserController::class, 'update'])->name('update');
            Route::delete('/{peminjaman}', [PeminjamanUserController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::get('/debug-reset', function () {
    $email = 'loli@gmail.com';
    
    // 1. Cek user
    $user = \App\Models\User::where('email', $email)->first();
    echo "User: " . ($user ? $user->name : 'NOT FOUND') . "<br>";
    
    // 2. Cek token
    $token = \DB::table('password_reset_tokens')->where('email', $email)->first();
    echo "Token: " . ($token ? $token->token : 'NOT FOUND') . "<br>";
    
    // 3. Test reset
    if ($token && $user) {
        $status = \Illuminate\Support\Facades\Password::reset(
            [
                'email' => $email,
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
                'token' => $token->token
            ],
            function ($user, $password) {
                $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);
            }
        );
        echo "Status: " . $status . "<br>";
    }
});

Route::get('/debug-reset-detailed', function () {
    \DB::table('password_reset_tokens')->where('email', 'loli@gmail.com')->delete();
    echo "Token dihapus!";
});

require __DIR__.'/auth.php';
