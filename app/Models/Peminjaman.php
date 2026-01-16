<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'customer_id',
        'status',
        'tanggal_pinjam',
        'tanggal_kembali'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detail()
    {
        return $this->hasMany(PeminjamanDetail::class);
    }
}
