<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // pastikan ini Facade
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Ambil input email dan password
        $credentials = $request->only('email', 'password');

        // Login pakai web session
        if (Auth::attempt($credentials)) {
            // Regenerate session supaya aman
            $request->session()->regenerate();

            // Redirect sesuai role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin'); // beranda admin
            } else {
                return redirect()->intended('/home'); // beranda user
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Logout dari web session secara aman
        Auth::guard('web')->logout(); // <- perbaikan utama

        // Hapus session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // API
    public function apiLogin(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'user'    => $user,
        'token'   => $token
    ]);
}
public function apiLogout(Request $request)
{
    $user = $request->user(); // ambil user dari token

    if ($user) {
        $user->tokens()->delete(); // hapus semua token aktif
    }

    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil'
    ]);
}

}
