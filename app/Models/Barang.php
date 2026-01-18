<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;

class Barang extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'barangs';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'nama_barang',
        'category_id',
        'kode_barang',
        'stok',
        'deskripsi',
        'image',
    ];

    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }
}
