<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;

class PeminjamanAdminController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['customer', 'detail.barang'])->get();
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    public function approve($id)
    {
        Peminjaman::where('id', $id)->update(['status' => 'aktif']);
        return back();
    }

    public function reject($id)
    {
        Peminjaman::where('id', $id)->update(['status' => 'nonaktif']);
        return back();
    }

    public function return($id)
    {
        Peminjaman::where('id', $id)->update(['status' => 'done']);
        return back();
    }
}
