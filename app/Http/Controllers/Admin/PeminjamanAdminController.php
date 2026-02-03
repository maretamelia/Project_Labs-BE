<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamanAdminController extends Controller
{
    // Daftar peminjaman aktif
    public function daftarPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['pending', 'peminjaman', 'disetujui', 'pengembalian', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.peminjaman.daftar', compact('peminjamans'));
    }

    // Riwayat peminjaman (selesai/ditolak)
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'barang'])
            ->whereIn('status', ['ditolak', 'dikembalikan'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.peminjaman.riwayat', compact('peminjamans'));
    }

    // Approve peminjaman
    public function approvePeminjaman(Peminjaman $peminjaman)
    {
        $barang = $peminjaman->barang;

        if(!in_array($peminjaman->status, ['pending', 'peminjaman', 'pengembalian'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status ini tidak bisa diterima'
            ]);
        }

        switch($peminjaman->status) {
            case 'pending':
            case 'peminjaman':
                // Kurangi stok saat disetujui, hanya jika belum dikurangi
                if ($peminjaman->status === 'pending') {
                    if ($barang->stok < $peminjaman->jumlah_pinjam) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok barang tidak mencukupi'
                        ]);
                    }
                    $barang->decrement('stok', $peminjaman->jumlah_pinjam);
                }
                $peminjaman->status = 'disetujui';
                break;

            case 'pengembalian':
                $today = Carbon::today();
                if($today->gt(Carbon::parse($peminjaman->tanggal_pengembalian))) {
                    $peminjaman->status = 'terlambat';
                } else {
                    $peminjaman->status = 'dikembalikan';
                }
                // Tambahkan stok kembali saat dikembalikan
                $barang->increment('stok', $peminjaman->jumlah_pinjam);
                break;
        }

        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }

    // Reject peminjaman
    public function rejectPeminjaman(Peminjaman $peminjaman)
    {
        $barang = $peminjaman->barang;

        if(!in_array($peminjaman->status, ['pending', 'peminjaman', 'pengembalian', 'terlambat'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status ini tidak bisa ditolak'
            ]);
        }

        // Kembalikan stok jika peminjaman dibatalkan
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
    public function show($id)
{
    $peminjaman = Peminjaman::with('user', 'barang')->findOrFail($id);
    return view('admin.peminjaman.show', compact('peminjaman'));
}

}
