<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamanAdminController extends Controller
{
    public function daftarPeminjaman()
    {
        $peminjamans = Peminjaman::with('user')
            ->whereIn('status', ['peminjaman', 'disetujui', 'pengembalian', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.peminjaman.daftar', compact('peminjamans'));
    }

    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('user')
            ->whereIn('status', ['ditolak', 'dikembalikan'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.peminjaman.riwayat', compact('peminjamans'));
    }

    public function approvePeminjaman(Peminjaman $peminjaman)
    {
        if(!in_array($peminjaman->status, ['peminjaman', 'pengembalian'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status ini tidak bisa diterima'
            ]);
        }

        switch($peminjaman->status) {
            case 'peminjaman':
                $peminjaman->status = 'disetujui';
                break;

            case 'pengembalian':
                $today = Carbon::today();
                if($today->gt(Carbon::parse($peminjaman->tanggal_kembali))) {
                    $peminjaman->status = 'terlambat';
                } else {
                    $peminjaman->status = 'dikembalikan';
                }
                break;
        }

        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }

    public function rejectPeminjaman(Peminjaman $peminjaman)
    {
        if(!in_array($peminjaman->status, ['peminjaman', 'pengembalian', 'terlambat'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status ini tidak bisa ditolak'
            ]);
        }

        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }
}
