<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardUserController extends Controller
{
    public function index()
    {
        // Ambil user login
        $userId = Auth::id();

        // ❗ SAFETY: kalau belum login
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User belum login'
            ], 401);
        }

        $stats = [
            'dipinjam'  => Peminjaman::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'menunggu'  => Peminjaman::where('user_id', $userId)->whereIn('status', ['pending', 'pending_back'])->count(),
            'terlambat' => Peminjaman::where('user_id', $userId)->where('status', 'terlambat')->count(),
            'kembali'   => Peminjaman::where('user_id', $userId)->where('status', 'selesai')->count(),
        ];

        $recent = Peminjaman::with('barang')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'namaBarang' => optional($item->barang)->nama_barang ?? 'Barang tidak ditemukan',
                    'jumlah' => $item->jumlah ?? 0,
                    'tanggalPinjam' => $item->tanggal_peminjaman
                        ? Carbon::parse($item->tanggal_peminjaman)->format('d/m/Y')
                        : '-',
                    'tanggalKembali' => $item->tanggal_pengembalian
                        ? Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y')
                        : '-',
                    'status' => ucfirst($item->status ?? '-'),
                ];
            });

        $reminders = Peminjaman::with('barang')
            ->where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->whereNotNull('tanggal_pengembalian')
            ->whereBetween('tanggal_pengembalian', [now(), now()->addDays(3)])
            ->get()
            ->map(function ($item) {
                $tanggalKembali = Carbon::parse($item->tanggal_pengembalian);
                $selisihHari = now()->diffInDays($tanggalKembali, false);

                $pesan = $selisihHari <= 0
                    ? 'Hari ini'
                    : ($selisihHari + 1) . ' hari lagi';

                return [
                    'namaBarang' => optional($item->barang)->nama_barang ?? '-',
                    'pesan' => $pesan
                ];
            });

        $rules = [
            'Maksimal 3 barang per peminjaman',
            'Keterlambatan dikenakan sanksi',
            'Barang rusak wajib dilaporkan'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard user berhasil diambil',
            'data' => [
                'stats'     => $stats,
                'recent'    => $recent,
                'reminders' => $reminders,
                'rules'     => $rules
            ]
        ]);
    }
}
