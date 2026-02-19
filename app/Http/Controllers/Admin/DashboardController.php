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

    // endpoint /dashboard (optional)
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard admin endpoint OK'
        ]);
    }

    // endpoint /dashboard-stats (INI YANG DIPAKAI REACT)
    public function dashboardStats(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $totalBarang = Barang::sum('stok');
        $jumlahDipinjam = Peminjaman::whereIn('status', [
            'disetujui', 'peminjaman', 'pengembalian', 'terlambat'
        ])->count();

        $jumlahSelesai = Peminjaman::where('status', 'dikembalikan')->count();

        $lowStockItems = Barang::where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get()
            ->map(fn ($barang) => [
                'name' => $barang->nama_barang,
                'count' => $barang->stok,
                'color' => $barang->stok == 0
                    ? '#ff4d4f'
                    : ($barang->stok <= 3 ? '#ffa502' : '#7367f0')
            ]);

        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agst','Sep','Okt','Nov','Des'];
        $chartData = [];

        foreach ($months as $i => $month) {
            $m = $i + 1;

            $chartData[] = [
                'month' => $month,
                'peminjaman' => Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $m)->count(),
                'pengembalian' => Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $m)->where('status','dikembalikan')->count(),
                'terlambat' => Peminjaman::whereYear('created_at', $year)->whereMonth('created_at', $m)->where('status','terlambat')->count(),
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
