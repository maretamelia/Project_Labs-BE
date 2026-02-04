@extends('layouts.admin')

@section('content')
<h1>Edit Kategori</h1>

@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color:red">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form edit kategori -->
<form action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}">
    <button type="submit">Update</button>
</form>

<!-- Tombol kembali -->
<a href="{{ route('admin.kategori.index') }}">Kembali</a>
@endsection
