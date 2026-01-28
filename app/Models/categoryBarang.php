<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBarang extends Model
{
    use SoftDeletes; // Untuk support deleted_at

    protected $table = 'category_barangs';
    protected $fillable = [
        'kategori',
    ];

    // Relasi ke tabel barangs
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori'); 
    }
}
