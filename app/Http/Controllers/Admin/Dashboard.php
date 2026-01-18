<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403); // akses ditolak kalau bukan admin
        }

        return view('admin.dashboard'); // Blade khusus admin
    }
}
