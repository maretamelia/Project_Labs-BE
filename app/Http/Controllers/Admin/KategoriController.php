<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // ✨ Tampil semua kategori
    public function index()
    {
        // Ambil semua data kategori dari database
        $kategori = Kategori::all();
        // Kirim data ke view index.blade.php
        return view('admin.kategori.index', compact('kategori'));
    }

    // ✨ Tampil form tambah kategori
    public function create()
    {
        // Cukup panggil view create.blade.php
        return view('admin.kategori.create');
    }

    // ✨ Simpan kategori baru ke database
    public function store(Request $request)
    {
        // Validasi input, nama_kategori wajib diisi
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // Simpan ke database
        Kategori::create($request->all());

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    // ✨ Tampil form edit kategori
    public function edit(Kategori $kategori)
    {
        // Kirim data kategori yang mau diedit ke view edit.blade.php
        return view('admin.kategori.edit', compact('kategori'));
    }

    // ✨ Update kategori di database
    public function update(Request $request, Kategori $kategori)
    {
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // Update data
        $kategori->update($request->all());

        // Redirect ke index dengan pesan sukses
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diubah');
    }

    // ✨ Hapus kategori dari database
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        // Redirect ke index dengan pesan sukses
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
