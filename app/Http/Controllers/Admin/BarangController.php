<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Tampilkan semua barang
    public function index()
    {
        $barang = Barang::all();
        return view('admin.barang.index', compact('barang'));
    }

    // Form tambah barang
    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.barang.create', compact('kategori'));
    }

    // Simpan barang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barang,id',
            'kode_barang' => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'image'       => 'nullable|image|max:2048', // maksimal 2MB
        ]);

        // Handle upload gambar
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    // Form edit barang
    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        return view('admin.barang.edit', compact('barang', 'kategori'));
    }

    // Update barang
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barang,id',
            'kode_barang' => 'required|string|max:100',
            'stok'        => 'required|integer',
            'deskripsi'   => 'nullable|string',
            'image'       => 'nullable|image|max:2048', // maksimal 2MB
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            // opsional: hapus image lama dulu sebelum update
            // Storage::disk('public')->delete($barang->image);
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diubah');
    }

    // Hapus barang
    public function destroy(Barang $barang)
    {
        // opsional: hapus file gambar juga
        // Storage::disk('public')->delete($barang->image);

        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
    // Tampilkan detail barang
    public function show(Barang $barang)
    {
    return view('admin.barang.show', compact('barang'));
    }

}
