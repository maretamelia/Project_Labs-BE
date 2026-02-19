<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;

class PeminjamanAdminController extends Controller
{
    /* =====================
       ======= WEB =========
       ===================== */

    public function daftarPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['pending', 'disetujui', 'pengembalian', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.peminjaman.daftar', compact('peminjamans'));
    }

    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['ditolak', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.peminjaman.riwayat', compact('peminjamans'));
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['user', 'barang'])->findOrFail($id);
        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    /* =====================
       ======= API =========
       ===================== */

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    // GET api/admin/peminjaman
    public function apiIndex()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['pending', 'disetujui', 'pengembalian', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peminjamans
        ]);
    }

    // POST api/admin/peminjaman/{id}/approve
    public function apiApprove($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Update status ke "selesai" (status valid di DB)
        $peminjaman->status = 'selesai';
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disetujui',
            'data' => $peminjaman
        ]);
    }

    // POST api/admin/peminjaman/{id}/reject
    public function apiReject($id, Request $request)
    {
        $request->validate([
            'alasan' => 'required|string'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'ditolak';
        $peminjaman->keterangan = $request->alasan;
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman ditolak',
            'data' => $peminjaman
        ]);
    }

    // GET api/admin/peminjaman/riwayat
    public function apiRiwayat()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['ditolak', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peminjamans
        ]);
    }

    // GET api/admin/peminjaman/{id}
    public function apiShow($id)
    {
        $peminjaman = Peminjaman::with(['user', 'barang'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $peminjaman
        ]);
    }

    // POST api/peminjaman/{id}/kembalikan
    public function apiUserKembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa mengembalikan'
            ], 400);
        }

        $peminjaman->status = 'pengembalian';
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }
}
