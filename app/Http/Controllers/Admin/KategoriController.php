<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori; // import model

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all(); // ambil semua kategori
        return view('admin.kategori.index', compact('kategori')); // kirim ke view
    }
}
