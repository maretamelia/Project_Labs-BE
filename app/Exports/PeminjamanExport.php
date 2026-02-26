<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Judul kolom di Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Peminjam',
            'Nama Barang',
            'Jumlah',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Status',
            'Keterangan'
        ];
    }

    /**
     * Memetakan data dari database ke kolom Excel
     */
    public function map($peminjaman): array
    {
        static $no = 1;
        return [
            $no++,
            $peminjaman->user->name ?? '-',
            $peminjaman->barang->nama_barang ?? '-',
            $peminjaman->jumlah,
            $peminjaman->tanggal_peminjaman,
            $peminjaman->tanggal_pengembalian,
            ucfirst($peminjaman->status),
            $peminjaman->keterangan ?? '-'
        ];
    }
}