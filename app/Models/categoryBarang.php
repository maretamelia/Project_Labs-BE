<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_barangs'; // Sesuaikan dengan nama tabel di database

    protected $fillable = ['nama_kategori'];

    // Relasi ke Barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'category_id');
    }
}
