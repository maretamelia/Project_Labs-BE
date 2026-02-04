<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamanAdminController extends Controller
{
    /* =====================
       ======= WEB =========
       ===================== */

    public function daftarPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['pending', 'peminjaman', 'disetujui', 'pengembalian', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.peminjaman.daftar', compact('peminjamans'));
    }

    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['ditolak', 'dikembalikan'])
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
            ->whereIn('status', ['pending', 'peminjaman', 'disetujui', 'pengembalian', 'terlambat'])
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
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);
        $barang = $peminjaman->barang;

        if ($peminjaman->status === 'pending') {

            if ($barang->stok < $peminjaman->jumlah_pinjam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok barang tidak mencukupi'
                ], 400);
            }

            $barang->decrement('stok', $peminjaman->jumlah_pinjam);
            $peminjaman->status = 'disetujui';
        }
        elseif ($peminjaman->status === 'pengembalian') {

            $today = Carbon::today();

            if ($today->gt($peminjaman->tanggal_pengembalian)) {
                $peminjaman->status = 'terlambat';
            } else {
                $peminjaman->status = 'dikembalikan';
            }

            $barang->increment('stok', $peminjaman->jumlah_pinjam);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak bisa diproses'
            ], 400);
        }

        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }

    // POST api/admin/peminjaman/{id}/reject
    public function apiReject($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);
        $barang = $peminjaman->barang;

        if (!in_array($peminjaman->status, ['pending', 'peminjaman', 'pengembalian', 'terlambat'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak bisa ditolak'
            ], 400);
        }

        if (in_array($peminjaman->status, ['pending', 'peminjaman', 'terlambat'])) {
            $barang->increment('stok', $peminjaman->jumlah_pinjam);
        }

        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }
    // GET api/admin/peminjaman/riwayat
public function apiRiwayat()
{
    $peminjamans = Peminjaman::with(['user', 'barang'])
        ->whereIn('status', ['ditolak', 'dikembalikan'])
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


}
