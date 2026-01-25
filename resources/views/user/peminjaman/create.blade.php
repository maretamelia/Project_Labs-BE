@extends('layouts.admin')

@section('content')
<h1>Ajukan Peminjaman</h1>

@if($errors->any())
    <div style="color:red">
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- form peminjaman -->
<form action="{{ route('user.peminjaman.store') }}" method="POST">
    @csrf
    <label>Barang:</label>
    <select name="barang_id">
        @foreach($barangs as $barang)
            <option value="{{ $barang->id }}">{{ $barang->nama }} (stok: {{ $barang->stok }})</option>
        @endforeach
    </select>

    <label>Jumlah:</label>
    <input type="number" name="jumlah" value="{{ old('jumlah') }}" />

    <label>Tanggal Pinjam:</label>
    <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}" />

    <label>Tanggal Kembali:</label>
    <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}" />

    <label>Keterangan:</label>
    <textarea name="keterangan">{{ old('keterangan') }}</textarea>

    <button type="submit">Ajukan Peminjaman</button>
</form>
