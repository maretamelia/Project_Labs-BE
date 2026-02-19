<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /* =========================
     | API - LIST PEMINJAMAN USER
     ========================= */
    public function apiIndex()
    {
        $user = Auth::user();
        $peminjamans = Peminjaman::with(['barang.kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peminjamans
        ]);
    }

    /* =========================
     | API - RIWAYAT PEMINJAMAN USER
     ========================= */
    public function apiRiwayat()
    {
        $user = Auth::user();
        $peminjamans = Peminjaman::with(['barang.kategori'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['ditolak', 'dikembalikan'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peminjamans
        ]);
    }

    /* =========================
     | API - SIMPAN PEMINJAMAN
     ========================= */
public function apiStore(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'jumlah' => 'required|integer|min:1',
        'tanggal_peminjaman' => 'required|date',
        'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_peminjaman',
        'keterangan' => 'nullable|string|max:500',
    ]);

    $barang = Barang::findOrFail($request->barang_id);

    if ($barang->stok < $request->jumlah) {
        return response()->json([
            'success' => false,
            'message' => 'Stok barang tidak mencukupi'
        ], 400);
    }

    $peminjaman = Peminjaman::create([
        'user_id' => $user->id,
        'barang_id' => $barang->id,
        'jumlah' => $request->jumlah,
        'tanggal_peminjaman' => $request->tanggal_peminjaman,
        'tanggal_pengembalian' => $request->tanggal_pengembalian,
        'keterangan' => $request->keterangan,

        'status' => 'pending',
    ]);

    // $barang->decrement('stok', $request->jumlah);

    return response()->json([
        'success' => true,
        'message' => 'Peminjaman berhasil diajukan',
        'data' => $peminjaman
    ], 201);
}


    /* =========================
     | API - DETAIL PEMINJAMAN
     ========================= */
    public function apiShow($id)
    {
        $user = Auth::user();
        $peminjaman = Peminjaman::with('barang.kategori')->find($id);

        if (!$peminjaman || $peminjaman->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden atau data tidak ditemukan'], 403);
        }

        return response()->json(['success' => true, 'data' => $peminjaman]);
    }

    /* =========================
     | API - UPDATE PEMINJAMAN
     ========================= */
    public function apiUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman || $peminjaman->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden atau data tidak ditemukan'], 403);
        }

        if ($peminjaman->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Peminjaman tidak bisa diubah'], 400);
        }

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_peminjaman',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $barang = Barang::find($request->barang_id);
        if (!$barang) {
            return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);
        }

        $selisih = $request->jumlah - $peminjaman->jumlah;
        if ($selisih > 0 && $barang->stok < $selisih) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
        }

        $peminjaman->update([
            'barang_id' => $barang->id,
            'jumlah' => $request->jumlah,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'keterangan' => $request->keterangan,
        ]);

        if ($selisih > 0) $barang->decrement('stok', $selisih);
        elseif ($selisih < 0) $barang->increment('stok', abs($selisih));

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diperbarui',
            'data' => $peminjaman
        ]);
    }

    public function apiReturn($id)
    {
        $user = Auth::user();
        $peminjaman = Peminjaman::with('barang')->find($id);

        if (!$peminjaman) {
            return response()->json(['success' => false, 'message' => 'data tidak ditemukan'], 403);
        }

        if ($peminjaman->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses, untuk mengembalikan!'], 403);

        }

        if ($peminjaman->status !== 'disetujui') {
            return response()->json(['success' => false, 'message' => 'Peminjaman belum disetujui admin'], 400);
        }

        $peminjaman->update(['status' => 'pending_back']);
        return response()->json(['success' => true, 'message' => 'Pengembalian diajukan']);
    }

    /* =========================
     | API - HAPUS PEMINJAMAN
     ========================= */
    public function apiDestroy($id)
    {
        $user = Auth::user();
        $peminjaman = Peminjaman::with('barang')->find($id);

        if (!$peminjaman || $peminjaman->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden atau data tidak ditemukan'], 403);
        }

        if ($peminjaman->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya peminjaman pending yang bisa dibatalkan'], 400);
        }

        if ($peminjaman->barang) {
            $peminjaman->barang->increment('stok', $peminjaman->jumlah);
        }

        $peminjaman->delete();

        return response()->json(['success' => true, 'message' => 'Peminjaman dibatalkan']);
    }
}

