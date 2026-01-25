<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $guarded = [];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // Contoh fungsi helper
    public function isOverdue()
    {
        return $this->tanggal_kembali < now();
    }
}
