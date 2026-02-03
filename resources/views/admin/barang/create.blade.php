@extends('layouts.admin')

@section('content')
<h1>Tambah Barang</h1>

@if($errors->any())
    <div style="color:red">
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tambahkan enctype="multipart/form-data" supaya bisa upload file -->
<form action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Nama Barang:</label><br>
    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"><br><br>

    <label>Kategori:</label><br>
    <select name="category_id">
        @foreach($kategori as $k)
            <option value="{{ $k->id }}" {{ old('category_id') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select><br><br>

    <label>Kode Barang:</label><br>
    <input type="text" name="kode_barang" value="{{ old('kode_barang') }}"><br><br>


    <label>Stok:</label><br>
    <input type="number" name="stok" value="{{ old('stok') }}"><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi">{{ old('deskripsi') }}</textarea><br><br>

    <label>Gambar:</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit">Simpan</button>
</form>

<a href="{{ route('admin.barang.index') }}">Kembali</a>
@endsection
