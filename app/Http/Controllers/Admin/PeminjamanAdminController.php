<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
    $user = Auth::user();
    $peminjaman = Peminjaman::with('barang')->findOrFail($id);

    if ($peminjaman->status === 'pending_back') {

        $today = Carbon::now()->toDateString();
        $batas = Carbon::parse($peminjaman->tanggal_pengembalian)->toDateString();

        $peminjaman->tanggal_pengembalian_selesai = now();

        if ($today > $batas) {
            $peminjaman->status = 'terlambat';
            $peminjaman->keterangan = 'Terlambat pengembalian';

            Notification::create([
                'user_id' => $user->id, // ID pengguna yang mengirim notifikasi
                'to_user_id' => $peminjaman->user_id, // ID pengguna yang akan menerima notifikasi
                'peminjaman_id' => $peminjaman->id,
                'title' => 'Menyetujui Pengembalian Terlambat',
                'body' => 'Pengembalian barang ' . $peminjaman->barang->nama . ' telah diterima!',
            ]);
        } else {
            $peminjaman->status = 'selesai';

            Notification::create([
                'user_id' => $user->id, // ID pengguna yang mengirim notifikasi
                'to_user_id' => $peminjaman->user_id, // ID pengguna yang akan menerima notifikasi
                'peminjaman_id' => $peminjaman->id,
                'title' => 'Menyetujui Pengembalian',
                'body' => 'Pengembalian barang ' . $peminjaman->barang->nama . ' telah diterima!',
            ]);
        }

        $peminjaman->barang->increment('stok', $peminjaman->jumlah);
    }
    else if ($peminjaman->status === 'pending') {
        $peminjaman->status = 'disetujui';
        $peminjaman->barang->decrement('stok', $peminjaman->jumlah);

        Notification::create([
            'user_id' => $user->id, // ID pengguna yang mengirim notifikasi
            'to_user_id' => $peminjaman->user_id, // ID pengguna yang akan menerima notifikasi
            'peminjaman_id' => $peminjaman->id,
            'title' => 'Menyetujui Peminjaman',
            'body' => 'Peminjaman barang ' . $peminjaman->barang->nama . ' berhasil disetujui',
        ]);
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

        $user = Auth::user();
        $peminjaman = Peminjaman::findOrFail($id);
        $oldStatus = $peminjaman->status;

        $peminjaman->status = 'ditolak';
        $peminjaman->keterangan = $request->alasan;
        $peminjaman->save();

        if ($oldStatus === 'pending') {
            Notification::create([
                'user_id' => $user->id, // ID pengguna yang mengirim notifikasi
                'to_user_id' => $peminjaman->user_id, // ID pengguna yang akan menerima notifikasi
                'peminjaman_id' => $peminjaman->id,
                'title' => 'Menolak Peminjaman',
                'body' => 'Peminjaman barang ' . $peminjaman->barang->nama . ' telah ditolak karena: ' . $request->alasan,
            ]);
        } else if ($oldStatus === 'pending_back') {
            Notification::create([
                'user_id' => $user->id, // ID pengguna yang mengirim notifikasi
                'to_user_id' => $peminjaman->user_id, // ID pengguna yang akan menerima notifikasi
                'peminjaman_id' => $peminjaman->id,
                'title' => 'Menolak Pengembalian',
                'body' => 'Pengembalian barang ' . $peminjaman->barang->nama . ' telah ditolak karena: ' . $request->alasan,
            ]);
        }

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
