<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil ID user yang sedang login
        $userId = Auth::id();

        // 2. Hitung statistik khusus untuk user ini saja
        $stats = [
            'dipinjam'  => Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count(),
            'menunggu'  => Peminjaman::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'terlambat' => Peminjaman::where('user_id', $userId)->where('status', 'terlambat')->count(),
            'kembali'   => Peminjaman::where('user_id', $userId)->where('status', 'dikembalikan')->count(),
        ];

        // 3. Ambil 5 data peminjaman terbaru untuk tabel di dashboard
        $recentPeminjaman = Peminjaman::with('barang') // Pastikan ada relasi 'barang' di model Peminjaman
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'no'             => $item->id,
                    'namaBarang'     => $item->barang->nama_barang ?? 'Barang tidak ditemukan',
                    'jumlah'         => $item->jumlah_pinjam,
                    'tanggalPinjam'  => $item->tanggal_pinjam ? $item->tanggal_pinjam->format('d/m/Y') : '-',
                    'tanggalKembali' => $item->tanggal_kembali ? $item->tanggal_kembali->format('d/m/Y') : '-',
                    'status'         => ucfirst($item->status),
                ];
            });

        // 4. Return sebagai JSON untuk dikonsumsi React
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard user berhasil diambil',
            'data'    => [
                'stats'  => $stats,
                'recent' => $recentPeminjaman
            ]
        ]);
        // 5. Logic Pengingat Pengembalian (Barang yang jatuh tempo dalam 3 hari)
$reminders = Peminjaman::with('barang')
    ->where('user_id', $userId)
    ->where('status', 'dipinjam')
    ->whereBetween('tanggal_kembali', [now(), now()->addDays(3)])
    ->get()
    ->map(function($item) {
        $diff = now()->diffInDays($item->tanggal_kembali, false);
        $pesan = $diff == 0 ? "Hari ini" : ($diff + 1) . " hari lagi";
        return [
            'nama' => $item->barang->nama_barang,
            'pesan' => $pesan
        ];
    });

// 6. Data Aturan Peminjaman (Bisa statis di sini atau ambil dari DB)
$rules = [
    "Maks. 3 barang",
    "Keterlambatan dikenakan sanksi",
    "Barang rusak wajib dilaporkan"
];

return response()->json([
    'success' => true,
    'data'    => [
        'stats'  => $stats,
        'recent' => $recentPeminjaman,
        'reminders' => $reminders, // Data baru
        'rules' => $rules          // Data baru
    ]
]);
    }
}