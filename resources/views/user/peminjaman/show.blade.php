@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Detail Peminjaman</h4>

    <table class="table table-bordered">
        <tr>
            <th>Nama Barang</th>
            <td>{{ $peminjaman->barang->nama_barang ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kode Barang</th>
            <td>{{ $peminjaman->barang->kode_barang ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $peminjaman->barang->kategori->nama_kategori ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jumlah Pinjam</th>
            <td>{{ $peminjaman->jumlah_pinjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Peminjaman</th>
            <td>{{ $peminjaman->tanggal_peminjaman->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Pengembalian</th>
            <td>{{ $peminjaman->tanggal_pengembalian ? $peminjaman->tanggal_pengembalian->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $peminjaman->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($peminjaman->status) }}</td>
        </tr>
    </table>

    <a href="{{ route('user.peminjaman.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
