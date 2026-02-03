@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-4">Edit Peminjaman</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('user.peminjaman.update', $peminjaman->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Pilih Barang -->
        <div class="mb-3">
            <label for="barang_id">Barang</label>
            <select name="barang_id" id="barang_id" class="form-control">
                @foreach($barang as $b)
                    <option value="{{ $b->id }}" {{ $b->id == $peminjaman->barang_id ? 'selected' : '' }}>
                        {{ $b->nama_barang }} ({{ $b->kategori->kategori ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jumlah Pinjam -->
        <div class="mb-3">
            <label for="jumlah_pinjam">Jumlah</label>
            <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" class="form-control"
                   value="{{ $peminjaman->jumlah_pinjam }}" min="1">
        </div>

        <!-- Tanggal Pinjam -->
        <div class="mb-3">
            <label for="tanggal_peminjaman">Tanggal Peminjaman</label>
            <input type="date" name="tanggal_peminjaman" id="tanggal_peminjaman" class="form-control"
                   value="{{ $peminjaman->tanggal_peminjaman?->format('Y-m-d') }}">
        </div>

        <!-- Tanggal Pengembalian -->
        <div class="mb-3">
            <label for="tanggal_pengembalian">Tanggal Pengembalian</label>
            <input type="date" name="tanggal_pengembalian" id="tanggal_pengembalian" class="form-control"
                   value="{{ $peminjaman->tanggal_pengembalian?->format('Y-m-d') }}">
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control">{{ $peminjaman->keterangan }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Peminjaman</button>
    </form>
</div>
@endsection
