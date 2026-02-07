<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // 1. Data untuk Stats Cards (Paling Atas)
        // Kita hitung stok fisik barang, bukan jumlah baris tabel
        $totalBarang = Barang::sum('stok'); 
        
        // Menghitung yang sedang diproses (belum selesai)
        $jumlahDipinjam = Peminjaman::whereIn('status', ['disetujui', 'peminjaman', 'pengembalian', 'terlambat'])->count();
        
        // Menghitung yang sudah tuntas
        $jumlahSelesai = Peminjaman::where('status', 'dikembalikan')->count();

        // 2. Data Barang Akan Habis (Stok <= 5)
        $lowStockItems = Barang::where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get()
            ->map(function($barang) {
                return [
                    'name' => $barang->nama_barang,
                    'count' => $barang->stok,
                    // Warna badge: Merah jika 0, Oranye jika <= 3, Biru jika lainnya
                    'color' => $barang->stok == 0 ? '#ff4d4f' : ($barang->stok <= 3 ? '#ffa502' : '#7367f0')
                ];
            });

        // 3. Data Grafik (Gabungan Line & Bar Chart)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agst', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartData = [];

        foreach ($months as $key => $monthName) {
            $monthNum = $key + 1;
            
            $peminjaman = Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $monthNum)->count();
            $pengembalian = Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $monthNum)->where('status', 'dikembalikan')->count();
            $terlambat = Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $monthNum)->where('status', 'terlambat')->count();

            $chartData[] = [
                'month' => $monthName,
                'peminjaman' => $peminjaman,
                'pengembalian' => $pengembalian,
                'terlambat' => $terlambat
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_barang' => $totalBarang,
                    'dipinjam' => $jumlahDipinjam,
                    'selesai' => $jumlahSelesai,
                ],
                'low_stock' => $lowStockItems,
                'charts' => $chartData
            ]
        ]);
    }
}