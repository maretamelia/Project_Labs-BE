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
    // Hanya menampilkan peminjaman aktif
    $activeStatuses = ['pending', 'disetujui', 'pending_back']; // pending_back = menunggu pengembalian

    $peminjamans = Peminjaman::with(['user', 'barang'])
        ->whereIn('status', $activeStatuses)
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

    if ($peminjaman->status === 'pending_back') {

        $today = Carbon::now()->toDateString();
        $batas = Carbon::parse($peminjaman->tanggal_pengembalian)->toDateString();

        $peminjaman->tanggal_pengembalian_selesai = now();

        if ($today > $batas) {
            $peminjaman->status = 'terlambat';
            $peminjaman->keterangan = 'Terlambat pengembalian';
        } else {
            $peminjaman->status = 'selesai';
        }

        $peminjaman->barang->increment('stok', $peminjaman->jumlah);
    }

    else if ($peminjaman->status === 'pending') {
        $peminjaman->status = 'disetujui';
        $peminjaman->barang->decrement('stok', $peminjaman->jumlah);
    }

    $peminjaman->save();

    return response()->json([
        'success' => true,
        'message' => 'Peminjaman berhasil diproses',
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
    // Hanya menampilkan peminjaman yang sudah selesai atau ditolak/terlambat
    $historyStatuses = ['selesai', 'ditolak', 'terlambat'];

    $peminjamans = Peminjaman::with(['user', 'barang'])
        ->whereIn('status', $historyStatuses)
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
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);

        if ($peminjaman->status !== 'disetujui' && $peminjaman->status !== 'pending_back') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa mengembalikan, karena belum disetujui peminjaman atau sudah dikembalikan'
            ], 400);
        }

        $peminjaman->status = 'pending_back';
        $peminjaman->save();

        return response()->json([
            'success' => true,
            'status' => $peminjaman->status
        ]);
    }
}
