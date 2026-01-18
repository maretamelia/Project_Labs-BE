<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlal',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }

    public function barang()
    {
        return $this->belongsTo(barang::class, 'barang_id');
    }
}
