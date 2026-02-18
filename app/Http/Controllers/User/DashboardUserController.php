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
            'dipinjam'  => Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count(),
            'menunggu'  => Peminjaman::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'terlambat' => Peminjaman::where('user_id', $userId)->where('status', 'terlambat')->count(),
            'kembali'   => Peminjaman::where('user_id', $userId)->where('status', 'dikembalikan')->count(),
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
                    'jumlah' => $item->jumlah_pinjam ?? 0,
                    'tanggalPinjam' => $item->tanggal_pinjam
                        ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y')
                        : '-',
                    'tanggalKembali' => $item->tanggal_kembali
                        ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y')
                        : '-',
                    'status' => ucfirst($item->status ?? '-'),
                ];
            });

        $reminders = Peminjaman::with('barang')
            ->where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->whereNotNull('tanggal_kembali')
            ->whereBetween('tanggal_kembali', [now(), now()->addDays(3)])
            ->get()
            ->map(function ($item) {
                $tanggalKembali = Carbon::parse($item->tanggal_kembali);
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
