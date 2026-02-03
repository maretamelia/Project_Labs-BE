<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;

class BarangUserController extends Controller
{
    // Tampilkan semua barang untuk user
    public function index()
    {
        // Ambil semua barang + relasi kategori
        $barang = Barang::with('kategori')->get();
        return view('user.barang.index', compact('barang'));
    }

    public function show(Barang $barang)
{
    $barang->load('kategori'); // pastikan kategori ikut muncul
    return view('user.barang.show', compact('barang'));
}

}
