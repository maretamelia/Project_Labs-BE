@extends('layouts.admin')

@section('content')
<h1>Detail Barang</h1>

<p><strong>Kode Barang:</strong> {{ $barang->kode_barang }}</p>
<p><strong>Nama Barang:</strong> {{ $barang->nama_barang }}</p>
<p><strong>Kategori:</strong> {{ $barang->kategori->nama_kategori ?? '-' }}</p>
<p><strong>Stok:</strong> {{ $barang->stok }}</p>
<p><strong>Deskripsi:</strong> {{ $barang->deskripsi }}</p>

@if($barang->image)
    <p><strong>Gambar:</strong></p>
    <img src="{{ asset('storage/' . $barang->image) }}" alt="{{ $barang->nama_barang }}" style="max-width:200px;">
@endif

<a href="{{ route('user.barang.index') }}">Kembali ke Daftar Barang</a>
@endsection
