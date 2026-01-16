<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'category_barang'; // nama tabel di DB
    protected $fillable = ['nama_kategori']; // kolom yang bisa diisi massal
}
