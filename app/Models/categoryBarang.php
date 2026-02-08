<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_barangs';

    protected $fillable = [
        'nama_kategori',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Relasi ke tabel barangs
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'category_id');
    }
}
