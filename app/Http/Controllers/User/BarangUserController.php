<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;

class BarangUserController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('category')->get();
        return view('user.barangs.index', compact('barangs'));
    }

    public function show(Barang $barang)
    {
        return view('user.barangs.show', compact('barang'));
    }
}
