@extends('layouts.admin')

@section('content')
<h1>Daftar Barang</h1>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barang as $b)
            <tr>
                <td>{{ $b->kode_barang }}</td>
                <td><a href="{{ route('user.barang.show', $b->id) }}">{{ $b->nama_barang }}</a></td>
                <td>{{ $b->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $b->stok }}</td>
                <td>{{ $b->deskripsi }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
