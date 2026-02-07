<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangUserController extends Controller
{
    // Fungsi API untuk React
    public function apiIndex()
    {
        try {
            // Kita panggil relasi 'kategori' yang sudah kamu buat di Model
            $barang = Barang::with('kategori')->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $barang
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            $barang = Barang::with('kategori')->find($id);
            if (!$barang) {
                return response()->json(['message' => 'Barang tidak ditemukan'], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $barang
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}