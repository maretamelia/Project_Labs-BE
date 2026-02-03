@extends('layouts.admin')

@section('content')
<h1>Detail Barang</h1>

<div style="border:1px solid #ccc; padding:20px; border-radius:8px; max-width:500px;">
    @if($barang->image)
        <img src="{{ asset('storage/'.$barang->image) }}" alt="{{ $barang->nama_barang }}" style="width:100%; height:250px; object-fit:cover; border-radius:5px;">
    @else
        <div style="width:100%; height:250px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:5px;">No Image</div>
    @endif

    <h2>{{ $barang->nama_barang }}</h2>
    <p><strong>Kategori:</strong> {{ $barang->kategori->nama_kategori ?? '-' }}</p>
    <p><strong>Kode Barang:</strong> {{ $barang->kode_barang }}</p>
    <p><strong>Harga:</strong> Rp {{ number_format($barang->harga,0,',','.') }}</p>
    <p><strong>Stok:</strong> {{ $barang->stok }}</p>
    <p><strong>Deskripsi:</strong><br>{{ $barang->deskripsi ?? '-' }}</p>
</div>

<br>
<a href="{{ route('admin.barang.index') }}">Kembali ke Daftar Barang</a>
@endsection
