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
        $barangs = Barang::with('kategori')->get();

        // 🔹 Tambahkan URL lengkap gambar
        $barangs->transform(function ($item) {
            $item->gambar = $item->image
                ? asset('storage/' . $item->image) // URL lengkap untuk FE
                : null;
            return $item;
        });

        return response()->json($barangs);
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
        'stok'        => 'required|integer',
        'deskripsi'   => 'nullable|string',
        'image'       => 'nullable|image|max:2048',
    ]);

    // 🔹 Ambil kategori
    $kategori = \App\Models\CategoryBarang::find($data['category_id']);

    // 🔹 Generate kode unik berdasarkan kategori
    $prefix = strtoupper(substr($kategori->nama_kategori, 0, 3));

    // Ambil kode terakhir dari kategori ini
    $lastBarang = \App\Models\Barang::where('category_id', $data['category_id'])
                    ->orderBy('id', 'desc')
                    ->first();

    if ($lastBarang) {
        // Ambil angka terakhir dari kode sebelumnya
        $lastNumber = (int) substr($lastBarang->kode_barang, 4); // misal "ATK-003" → ambil 003
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    $data['kode_barang'] = $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

    // 🔹 Simpan gambar kalau ada
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('barang_images', 'public');
    }

    $barang = \App\Models\Barang::create($data);
    $barang->load('kategori');

    return response()->json([
    'success' => true,
    'message' => 'Barang berhasil ditambahkan',
    'data'    => $barang], 201);
    }

    // 🔹 Update barang lewat API
    public function apiUpdate(Request $request, Barang $barang)
{
    $data = $request->validate([
        'nama_barang' => 'sometimes|string',
        'category_id' => 'sometimes|exists:category_barangs,id',
        'stok' => 'sometimes|integer',
        'deskripsi' => 'sometimes|string',
        'image' => 'nullable|image',
    ]);

    // 🔹 Jika kategori berubah, generate kode baru
    if (isset($data['category_id']) && $data['category_id'] != $barang->category_id) {
        $data['kode_barang'] = $this->generateKodeBarang($data['category_id']);
    }

    if ($request->hasFile('image')) {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }
        $data['image'] = $request->file('image')->store('barang_images', 'public');
    }

    $barang->update($data);
    $barang->load('kategori');

    return response()->json([
    'success' => true,
    'message' => 'Barang berhasil diubah',
    'data'    => $barang
]);
}

    public function apiDestroy(Barang $barang)
    {
        if ($barang->image) {
            Storage::disk('public')->delete($barang->image);
        }

        $barang->delete();
        return response()->json([
    'success' => true,
    'message' => 'Barang berhasil dihapus'
]);
    }
    // 🔹 Fungsi pembantu untuk generate kode (pindahkan logika dari store ke sini)
private function generateKodeBarang($categoryId)
{
    $kategori = \App\Models\CategoryBarang::find($categoryId);
    $prefix = strtoupper(substr($kategori->nama_kategori, 0, 3));

    $lastBarang = \App\Models\Barang::where('category_id', $categoryId)
                    ->orderBy('id', 'desc')
                    ->first();

    if ($lastBarang) {
        $lastNumber = (int) substr($lastBarang->kode_barang, 4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}
}
