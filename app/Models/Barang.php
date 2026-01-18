<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CategoryBarang;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'category_id',
        'nama_barang',
        'kode_barang',
        'stok',
        'image',
        'deskripsi'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBarang::class, 'category_id');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }
}
