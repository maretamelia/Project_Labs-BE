<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    // endpoint /dashboard (optional)
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard admin endpoint OK'
        ]);
    }

    // endpoint /dashboard-stats (dipakai React)
    public function dashboardStats(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // =======================
        // STATS
        // =======================
        $totalBarang = Barang::sum('stok');

        // jumlah barang yang sedang dipinjam → status disetujui
        $jumlahDipinjam = Peminjaman::where('status', 'disetujui')->count();

        // jumlah barang yang sudah selesai → status selesai atau terlambat
        $jumlahSelesai = Peminjaman::whereIn('status', ['selesai', 'terlambat'])->count();

        // barang dengan stok rendah
        $lowStockItems = Barang::where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get()
            ->map(fn($barang) => [
                'name' => $barang->nama_barang,
                'count' => $barang->stok,
                'color' => $barang->stok == 0
                    ? '#ff4d4f'
                    : ($barang->stok <= 3 ? '#ffa502' : '#7367f0')
            ]);

        // =======================
        // CHART DATA
        // =======================
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agst','Sep','Okt','Nov','Des'];
        $chartData = [];

        foreach ($months as $i => $month) {
            $m = $i + 1;

            $chartData[] = [
                'month' => $month,
                'peminjaman' => Peminjaman::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->where('status', 'disetujui')
                    ->count(),
                'pengembalian' => Peminjaman::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->whereIn('status', ['selesai', 'terlambat'])
                    ->count(),
                'terlambat' => Peminjaman::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->where('status', 'terlambat')
                    ->count(),
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
