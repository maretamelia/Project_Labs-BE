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

    // Tampilkan semua barang (web)
    public function index()
    {
        $barang = Barang::with('kategori')->get(); // load kategori
        return view('admin.barang.index', compact('barang'));
    }

    // Form tambah barang (web)
    public function create()
    {
        $kategori = CategoryBarang::all();
        return view('admin.barang.create', compact('kategori'));
    }

    // Simpan barang baru (web)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barangs,id',
            'kode_barang' => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'stok'        => 'required|integer',
            'harga'       => 'required|numeric',
            'image'       => 'nullable|image|max:2048', // maksimal 2MB
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang = Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    // Form edit barang (web)
    public function edit(Barang $barang)
    {
        $kategori = CategoryBarang::all();
        return view('admin.barang.edit', compact('barang', 'kategori'));
    }

    // Update barang (web)
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
        if ($request->hasFile('image')) {
            if ($barang->image) {
                Storage::disk('public')->delete($barang->image); // hapus gambar lama
            }
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diubah');
    }

    // Hapus barang (web)
    public function destroy(Barang $barang)
    {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }

        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    // Tampilkan detail barang (web)
    public function show(Barang $barang)
    {
        $barang->load('kategori'); // pastikan kategori ikut di-load
        return view('admin.barang.show', compact('barang'));
    }

    // ========================
    // API CONTROLLER
    // ========================

    // Tampilkan semua barang (API)
    public function apiIndex()
    {
        $barang = Barang::with('kategori')->get();
        return response()->json($barang);
    }

    // Tampilkan detail barang (API)
    public function apiShow(Barang $barang)
    {
        $barang->load('kategori');
        return response()->json($barang);
    }

    // Tambah barang (API)
    public function apiStore(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:category_barangs,id',
            'kode_barang' => 'required|string|max:100',
            'stok'        => 'required|integer',
            'deskripsi'   => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barang_images', 'public');
        }

        $barang = Barang::create($data);
        $barang->load('kategori');

        return response()->json($barang, 201);
    }

    // Update barang (API)
public function apiUpdate(Request $request, Barang $barang)
{
    $data = $request->validate([
        'nama_barang' => 'sometimes|string',
        'category_id' => 'sometimes|integer',
        'kode_barang' => 'sometimes|string',
        'stok' => 'sometimes|integer',
        'image' => 'nullable|image',
    ]);

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


    // Hapus barang (API)
    public function apiDestroy(Barang $barang)
    {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }

        $barang->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
