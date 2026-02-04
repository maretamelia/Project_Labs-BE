<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\CategoryBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    // ========================
    // WEB CONTROLLER
    // ========================

    public function index()
    {
        $barang = Barang::with('kategori')->get();
        return view('admin.barang.index', compact('barang'));
    }

    public function create()
    {
        $kategori = CategoryBarang::all();
        return view('admin.barang.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barangs,id',
            'kode_barang' => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'stok'        => 'required|integer',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // 🔹 Jika ada file gambar, simpan ke storage/public/barang_images
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang = Barang::create($data);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        $kategori = CategoryBarang::all();
        return view('admin.barang.edit', compact('barang', 'kategori'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'sometimes|string',
            'category_id' => 'sometimes|integer',
            'kode_barang' => 'sometimes|string',
            'stok' => 'sometimes|integer',
            'image' => 'nullable|image',
        ]);

        $data = $request->all();

        // 🔹 Jika ada file gambar baru, hapus yang lama lalu simpan yang baru
        if ($request->hasFile('image')) {
            if ($barang->image) {
                Storage::disk('public')->delete($barang->image);
            }
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang->update($data);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil diubah');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }

        $barang->delete();
        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori');
        return view('admin.barang.show', compact('barang'));
    }

    // ========================
    // API CONTROLLER
    // ========================

    public function apiIndex()
    {
        $barang = Barang::with('kategori')->get();
        return response()->json($barang);
    }

    public function apiShow(Barang $barang)
    {
        $barang->load('kategori');
        return response()->json($barang);
    }

    // 🔹 Tambah barang lewat API
    public function apiStore(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barangs,id',
            'kode_barang' => 'required|string|max:100',
            'stok'        => 'required|integer',
            'deskripsi'   => 'nullable|string',
            'image'       => 'nullable|image|max:2048', // optional
        ]);

        // 🔹 Jika ada file gambar di API, simpan
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang = Barang::create($data);
        $barang->load('kategori');

        return response()->json($barang, 201);
    }

    // 🔹 Update barang lewat API
    public function apiUpdate(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'nama_barang' => 'sometimes|string',
            'category_id' => 'sometimes|integer',
            'kode_barang' => 'sometimes|string',
            'stok' => 'sometimes|integer',
            'image' => 'nullable|image',
        ]);

        // 🔹 Jika ada file gambar baru, hapus yang lama dulu
        if ($request->hasFile('image')) {
            if ($barang->image) {
                Storage::disk('public')->delete($barang->image);
            }
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang->update($data);
        $barang->load('kategori');

        return response()->json($barang);
    }

    public function apiDestroy(Barang $barang)
    {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }

        $barang->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
