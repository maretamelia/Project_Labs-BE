@extends('layouts.admin')

@section('content')
<h1>Tambah Kategori</h1>

<!-- Tampilkan error validasi -->
@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color:red">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form tambah kategori -->
<form action="{{ route('kategori.store') }}" method="POST">
    @csrf
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}">
    <button type="submit">Simpan</button>
</form>

<!-- Tombol kembali -->
<a href="{{ route('kategori.index') }}">Kembali</a>
@endsection
