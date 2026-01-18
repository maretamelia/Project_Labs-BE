@extends('layouts.admin')

@section('content')
<h1>Daftar Barang</h1>

@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif

<a href="{{ route('barang.create') }}">+ Tambah Barang</a>

<div style="display:flex; flex-wrap:wrap; gap:20px; margin-top:20px;">
    @foreach($barang as $b)
        <div style="border:1px solid #ccc; padding:10px; width:200px; border-radius:8px;">
            <!-- Image -->
            @if($b->image)
                <img src="{{ asset('storage/'.$b->image) }}" alt="{{ $b->nama_barang }}" style="width:100%; height:150px; object-fit:cover; border-radius:5px;">
            @else
                <div style="width:100%; height:150px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:5px;">No Image</div>
            @endif

            <h3>{{ $b->nama_barang }}</h3>
            <p><strong>Kategori:</strong> {{ $b->kategori->nama_kategori ?? '-' }}</p>
            <p><strong>Stok:</strong> {{ $b->stok }}</p>

            <a href="{{ route('barang.edit', $b->id) }}">Edit</a> |
            <form action="{{ route('barang.destroy', $b->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin hapus barang ini?')">Hapus</button>
            </form> |
            <a href="{{ route('barang.show', $b->id) }}">Detail</a>
        </div>
    @endforeach
</div>
@endsection
