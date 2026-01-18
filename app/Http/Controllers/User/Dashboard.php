<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek apakah role user
        if (!auth()->user()->isUser()) {
            abort(403); // akses ditolak kalau bukan user
        }

        // Tampilkan view Blade khusus user
        return view('user.dashboard');
    }
}
