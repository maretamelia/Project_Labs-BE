@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Form Peminjaman</h4>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- INFO BARANG TERPILIH --}}
    <div class="card mb-3">
        @if ($barang->image)
            <img src="{{ asset('storage/'.$barang->image) }}"
                 class="card-img-top"
                 style="height:200px; object-fit:cover">
        @endif

        <div class="card-body">
            <h5>{{ $barang->nama_barang }}</h5>
            <small>
                Kategori: {{ $barang->kategori->nama ?? '-' }}
            </small><br>
            <span class="badge bg-success">
                Stok {{ $barang->stok }}
            </span>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('user.peminjaman.store') }}" method="POST">
        @csrf

        <input type="hidden" name="barang_id" value="{{ $barang->id }}">

        <div class="mb-3">
            <label>Jumlah Pinjam</label>
            <input type="number"
                   name="jumlah_pinjam"
                   class="form-control"
                   min="1"
                   required>
        </div>

        <div class="mb-3">
            <label>Tanggal Pinjam</label>
            <input type="date"
                   name="tanggal_peminjaman"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Tanggal Kembali</label>
            <input type="date"
                   name="tanggal_pengembalian"
                   class="form-control">
        </div>

        {{-- Keterangan / Deskripsi --}}
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="deskripsi"
                      class="form-control"
                      placeholder="Isi keterangan atau catatan tambahan..."></textarea>
        </div>

        <button class="btn btn-primary">
            Ajukan Peminjaman
        </button>
    </form>
</div>
@endsection
