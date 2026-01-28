<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Approve peminjaman
    public function approvePeminjaman(Peminjaman $peminjaman)
    {
        $peminjaman->update(['status' => 'disetujui']);
        
        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disetujui',
            'data' => $peminjaman
        ]);
    }

    // Reject peminjaman
    public function rejectPeminjaman(Peminjaman $peminjaman, Request $request)
    {
        $request->validate([
            'alasan' => 'required|string'
        ]);

        $peminjaman->update([
            'status' => 'ditolak',
            'keterangan' => $request->alasan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Peminjaman ditolak',
            'data' => $peminjaman
        ]);
    }
}