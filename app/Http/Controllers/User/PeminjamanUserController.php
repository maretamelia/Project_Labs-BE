<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanUserController extends Controller
{
    // =========================
    // LIST PEMINJAMAN USER
    // =========================
    public function index()
    {
        $peminjamans = Peminjaman::with(['barang.kategori'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.peminjaman.index', compact('peminjamans'));
    }

    // =========================
    // PILIH BARANG
    // =========================
    public function create()
    {
        $barang = Barang::with('kategori')->get();
        return view('user.peminjaman.create', compact('barang'));
    }

    // =========================
    // FORM PEMINJAMAN
    // =========================
    public function form(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id'
        ]);

        $barang = Barang::with('kategori')->findOrFail($request->barang_id);

        return view('user.peminjaman.form', compact('barang'));
    }

    // =========================
    // SIMPAN PEMINJAMAN
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_peminjaman',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // CEK STOK
        if ($barang->stok < $request->jumlah_pinjam) {
            return back()->with('error', 'Stok barang tidak mencukupi');
        }

        // SIMPAN PEMINJAMAN
        Peminjaman::create([
            'user_id' => Auth::id(),
            'barang_id' => $barang->id,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'keetrangan' => $request->deskripsi,
            'status' => 'pending',
        ]);

        // KURANGI STOK
        $barang->decrement('stok', $request->jumlah_pinjam);

        return redirect()->route('user.peminjaman.index')
                         ->with('success', 'Peminjaman berhasil diajukan');
    }

    // =========================
    // DETAIL PEMINJAMAN
    // =========================
    public function show(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        $peminjaman->load(['barang.kategori']);
        return view('user.peminjaman.show', compact('peminjaman'));
    }

    // =========================
    // EDIT PEMINJAMAN
    // =========================
    public function edit(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);
        $barang = Barang::with('kategori')->get();
        return view('user.peminjaman.edit', compact('peminjaman', 'barang'));
    }

    // =========================
    // UPDATE PEMINJAMAN
    // =========================
    public function update(Request $request, Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_peminjaman',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // HITUNG PERBEDAAN JUMLAH PINJAM UNTUK UPDATE STOK
        $selisih = $request->jumlah_pinjam - $peminjaman->jumlah_pinjam;

        if ($selisih > 0 && $barang->stok < $selisih) {
            return back()->with('error', 'Stok barang tidak mencukupi untuk menambah jumlah pinjam');
        }

        // UPDATE PEMINJAMAN
        $peminjaman->update([
            'barang_id' => $barang->id,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'keterangan' => $request->keterangan,
        ]);

        // UPDATE STOK
        $barang->decrement('stok', max($selisih, 0));
        if ($selisih < 0) {
            $barang->increment('stok', abs($selisih));
        }

        return redirect()->route('user.peminjaman.index')
                         ->with('success', 'Peminjaman berhasil diperbarui');
    }

    // =========================
    // RETURN / PENGEMBALIAN
    // =========================
    public function return(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);

        $today = \Carbon\Carbon::today();
        if ($today < $peminjaman->tanggal_pengembalian) {
            return back()->with('error', 'Belum waktunya pengembalian');
        }

        // KEMBALIKAN STOK
        $barang = $peminjaman->barang;
        $barang->increment('stok', $peminjaman->jumlah_pinjam);

        $peminjaman->status = 'dikembalikan';
        $peminjaman->save();

        return redirect()->route('user.peminjaman.index')->with('success', 'Barang berhasil dikembalikan');
    }

    // =========================
    // HAPUS PEMINJAMAN
    // =========================
    public function destroy(Peminjaman $peminjaman)
    {
        abort_if($peminjaman->user_id !== Auth::id(), 403);

        if ($peminjaman->status !== 'pending') {
            return redirect()->route('user.peminjaman.index')
                ->with('error', 'Hanya peminjaman yang pending yang bisa dibatalkan.');
        }

        // KEMBALIKAN STOK jika dibatalkan
        $barang = $peminjaman->barang;
        $barang->increment('stok', $peminjaman->jumlah_pinjam);

        $peminjaman->delete();

        return redirect()->route('user.peminjaman.index')
            ->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}
