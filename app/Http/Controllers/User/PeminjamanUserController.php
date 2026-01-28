<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\CategoryBarang;
use Illuminate\Support\Facades\Auth;

class PeminjamanUserController extends Controller
{
    // ====== BLADE ======
    public function index()
    {
        $peminjamans = Peminjaman::with('barang.kategori')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10); // untuk pagination

        return view('user.peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $kategoris = CategoryBarang::all(); // ambil semua kategori
        return view('user.peminjaman.create', compact('kategoris'));
    }

    public function edit(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        abort_if($peminjaman->status !== 'menunggu', 403, 'Peminjaman sudah diproses');

        $kategoris = CategoryBarang::all();
        return view('user.peminjaman.edit', compact('peminjaman', 'kategoris'));
    }

    public function store(Request $request)
    {
        return $this->savePeminjaman($request);
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        abort_if($peminjaman->status !== 'menunggu', 403, 'Peminjaman sudah diproses');

        return $this->savePeminjaman($request, $peminjaman);
    }

    public function destroy(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        abort_if($peminjaman->status !== 'menunggu', 403, 'Peminjaman sudah diproses');

        $peminjaman->delete();

        return redirect()
            ->route('user.peminjaman.index')
            ->with('success', 'Peminjaman berhasil dibatalkan');
    }

    // ====== API ======
    public function apiIndex()
    {
        $data = Peminjaman::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function apiShow(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);

        return response()->json(['success' => true, 'data' => $peminjaman]);
    }

    public function apiStore(Request $request)
    {
        $peminjaman = $this->savePeminjaman($request, null, true);
        return response()->json(['success' => true, 'data' => $peminjaman], 201);
    }

    public function apiUpdate(Request $request, Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        abort_if($peminjaman->status !== 'menunggu', 403, 'Peminjaman sudah diproses');

        $peminjaman = $this->savePeminjaman($request, $peminjaman, true);
        return response()->json(['success' => true, 'data' => $peminjaman]);
    }

    public function apiDestroy(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        abort_if($peminjaman->status !== 'menunggu', 403, 'Peminjaman sudah diproses');

        $peminjaman->delete();
        return response()->json(['success' => true, 'message' => 'Peminjaman berhasil dibatalkan']);
    }

    // ====== PRIVATE FUNCTION ======
    private function savePeminjaman(Request $request, Peminjaman $peminjaman = null, $isApi = false)
    {
        // VALIDASI REQUEST
        $request->validate([
            'kategori'        => 'required|string',
            'nama_barang'     => 'required|string|max:255',
            'jumlah'          => 'required|numeric|min:1',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan'      => 'nullable|string',
        ]);

        // // AMBIL KATEGORI BERDASARKAN NAMA
        // $kategori = CategoryBarang::where('kategori', $request->kategori)->first();

        // // BUAT BARANG BARU DENGAN NAMA BARANG DARI USER
        // $barang = Barang::firstOrCreate(
        //     [
        //         'nama' => $request->nama_barang,
        //         'kategori' => $request->kategori,
        //     ],
        //     ['stok' => 0] // stok awal 0 jika baru dibuat
        // );

        // // CEK STOK
        // if (!$barang->wasRecentlyCreated && $request->jumlah > $barang->stok) {
        //     $message = ['jumlah' => 'Jumlah melebihi stok barang'];
        //     if ($isApi) return response()->json(['success' => false, 'errors' => $message], 422);
        //     return back()->withErrors($message)->withInput();
        // }

        // DATA PEMINJAMAN
        $data = [
            'user_id'         => Auth::id(),
            'nama_barang'       => $request->nama_barang,
            'jumlah'          =>             $request->jumlah,
            'kategori'        => $request->kategori,
            'tanggal_pinjam'  => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'keterangan'      => $request->keterangan,
            'status'          => 'menunggu',
        ];

        if ($peminjaman) {
            $peminjaman->update($data);
        } else {
            $peminjaman = Peminjaman::create($data);
        }

        return $peminjaman;
    }
}
