<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * GET /api/admin/kategori
     * Ambil semua kategori (yang belum dihapus)
     */
    public function index()
    {
        $kategori = CategoryBarang::withCount('barangs')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $kategori
        ]);
    }
    public function getKategori()
{
    $kategori = CategoryBarang::all();

    return response()->json([
        'success' => true,
        'data' => $kategori
    ]);
}


    /**
     * POST /api/admin/kategori
     * Tambah kategori
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_barangs', 'nama_kategori')
                    ->whereNull('deleted_at')
            ]
        ], [
            'nama_kategori.unique' => 'Kategori dengan nama tersebut sudah ada'
        ]);

        $kategori = CategoryBarang::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    /**
     * PUT /api/admin/kategori/{id}
     * Update kategori
     */
    public function update(Request $request, $id)
    {
        $kategori = CategoryBarang::findOrFail($id);

        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_barangs', 'nama_kategori')
                    ->whereNull('deleted_at')
                    ->ignore($kategori->id)
            ]
        ], [
            'nama_kategori.unique' => 'Kategori dengan nama tersebut sudah ada'
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'data' => $kategori
        ]);
    }

    /**
     * DELETE /api/admin/kategori/{id}
     * Soft delete kategori
     */
    public function destroy($id)
    {
        $kategori = CategoryBarang::findOrFail($id);

        if ($kategori->barangs()->count() > 0) {
            return response()->json([
                'message' => 'Kategori masih digunakan oleh barang'
            ], 400);
        }

        $kategori->delete(); // soft delete

        return response()->json([
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
