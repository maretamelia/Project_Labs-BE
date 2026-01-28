<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $fillable = [
        'user_id',
        'nama_barang',
        'kategori',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke kategori_barangs
    public function kategoriBarang()
    {
        return $this->hasOne(categoryBarang::class, 'nama_kategori', 'kategori');
    }
}
