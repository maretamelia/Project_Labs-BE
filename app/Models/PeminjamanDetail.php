<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanDetail extends Model
{
    protected $table = 'peminjaman_detail';

    protected $fillable = [
        'peminjaman_id',
        'barang_id',
        'qty'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
