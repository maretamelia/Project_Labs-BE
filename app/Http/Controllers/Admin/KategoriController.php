<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryBarang;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // ✨ API: detail 1 kategori
    public function show(CategoryBarang $kategori)
    {
        return response()->json($kategori);
    }

    // ✨ List kategori (web + API)
    public function index()
    {
        // ambil semua kategori beserta jumlah barangnya
        $kategori = CategoryBarang::withCount('barangs')->get();

        // Kalau dipanggil dari API
        if (request()->is('api/*')) {
            return response()->json($kategori);
        }

        // Kalau dipanggil dari web
        return view('admin.kategori.index', compact('kategori'));
    }

    // ✨ Form tambah kategori (web)
    public function create()
    {
        return view('admin.kategori.create');
    }

    // ✨ Simpan kategori (web + API)
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // cek apakah kategori sudah ada
        if (CategoryBarang::where('nama_kategori', $request->nama_kategori)->exists()) {
            $msg = 'Kategori telah dibuat';
            if (request()->is('api/*')) {
                return response()->json(['message' => $msg], 400);
            }
            return redirect()->back()->with('error', $msg)->withInput();
        }

        $kategori = CategoryBarang::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // Kalau dipanggil dari API
        if (request()->is('api/*')) {
            return response()->json([
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori
            ], 201);
        }

        // Kalau dipanggil dari web
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    // ✨ Form edit kategori (web)
    public function edit(CategoryBarang $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    // ✨ Update kategori (web + API)
    public function update(Request $request, CategoryBarang $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // opsional: cek duplikat saat update
        if (CategoryBarang::where('nama_kategori', $request->nama_kategori)
            ->where('id', '!=', $kategori->id)
            ->exists()
        ) {
            $msg = 'Kategori telah dibuat';
            if (request()->is('api/*')) {
                return response()->json(['message' => $msg], 400);
            }
            return redirect()->back()->with('error', $msg)->withInput();
        }

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // Kalau dipanggil dari API
        if (request()->is('api/*')) {
            return response()->json([
                'message' => 'Kategori berhasil diubah',
                'data' => $kategori
            ]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diubah');
    }

    // ✨ Hapus kategori (web + API)
    public function destroy(CategoryBarang $kategori)
    {
        // cek apakah kategori masih punya barang
        if ($kategori->barangs()->count() > 0) {
            $msg = 'Kategori digunakan pada barang';
            if (request()->is('api/*')) {
                return response()->json(['message' => $msg], 400);
            }
            return redirect()->back()->with('error', $msg);
        }

        $kategori->delete();

        // Kalau dipanggil dari API
        if (request()->is('api/*')) {
            return response()->json([
                'message' => 'Kategori berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
    public function apiIndex() {
    // Tambahkan withCount('barangs') di sini!
    $kategori = CategoryBarang::withCount('barangs')->get(); 
    return response()->json($kategori);
    
}

}
