@extends('layouts.admin')

@section('content')
<h1>Daftar Kategori Barang</h1>

<!-- Tombol tambah kategori -->
<a href="{{ route('admin.kategori.create') }}" style="margin-bottom:10px; display:inline-block;">+ Tambah Kategori</a>

<!-- Pesan sukses setelah tambah / edit / hapus -->
@if(session('success'))
    <div style="color:green; margin-bottom:10px;">{{ session('success') }}</div>
@endif

<!-- Pesan error (misal duplicate atau delete gagal) -->
@if(session('error'))
    <div style="color:red; margin-bottom:10px;">{{ session('error') }}</div>
@endif

<!-- Cek apakah ada kategori -->
@if($kategori->count() > 0)
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Total Barang</th>
                <th>Created At</th> <!-- Tambahan kolom -->
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $index => $k)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $k->nama_kategori }}</td>
                    <td>{{ $k->barangs_count }}</td>
                    <td>{{ \Carbon\Carbon::parse($k->created_at)->format('Y-m-d') }}</td> <!-- Parse pakai Carbon -->
                    <td>
                        <!-- Tombol edit -->
                        <a href="{{ route('admin.kategori.edit', $k) }}" style="margin-right:5px;">Edit</a>
                        <!-- Tombol hapus -->
                        <form action="{{ route('admin.kategori.destroy', $k) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Belum ada kategori</p>
@endif
@endsection
