@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Ajukan Peminjaman</h2>

    {{-- Menampilkan error validasi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/api/user/peminjaman" method="POST">
    @csrf
        @csrf

        {{-- Kategori Barang --}}
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori Barang</label>
            <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->kategori }}" {{ old('kategori') == $kategori->kategori ? 'selected' : '' }}>
                        {{ $kategori->kategori }}
                    </option>
                @endforeach
            </select>

        </div>

        {{-- Nama Barang --}}
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control" 
                value="{{ old('nama_barang') }}" placeholder="Isi nama barang" required>
        </div>

        {{-- Jumlah --}}
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" id="jumlah" name="jumlah" class="form-control" 
                value="{{ old('jumlah') }}" min="1" required>
        </div>

        {{-- Tanggal Pinjam --}}
        <div class="mb-3">
            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
            <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="form-control" 
                value="{{ old('tanggal_pinjam') }}" required>
        </div>

        {{-- Tanggal Kembali --}}
        <div class="mb-3">
            <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
            <input type="date" id="tanggal_kembali" name="tanggal_kembali" class="form-control" 
                value="{{ old('tanggal_kembali') }}" 
                min="{{ old('tanggal_pinjam', date('Y-m-d')) }}">
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea id="keterangan" name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('user.peminjaman.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
