@extends('layouts.admin')

@section('content')
<h1>Daftar Kategori Barang</h1>

<!-- Tombol tambah kategori -->
<a href="{{ route('kategori.create') }}">+ Tambah Kategori</a>

<!-- Pesan sukses setelah tambah / edit / hapus -->
@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif

<!-- Cek apakah ada kategori -->
@if($kategori->count() > 0)
    <ul>
        @foreach($kategori as $k)
            <li>
                {{ $k->nama_kategori }}
                <!-- Tombol edit -->
                <a href="{{ route('kategori.edit', $k) }}">Edit</a>
                <!-- Tombol hapus -->
                <form action="{{ route('kategori.destroy', $k) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>Belum ada kategori</p>
@endif
@endsection
