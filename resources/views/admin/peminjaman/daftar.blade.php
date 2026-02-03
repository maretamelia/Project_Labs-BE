@extends('layouts.admin')

@section('content')
<h1>Daftar Peminjaman</h1>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama User</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($peminjamans as $index => $p)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $p->user->name ?? '-' }}</td>
            <td>{{ $p->barang->nama_barang ?? '-' }}</td>
            <td>{{ $p->jumlah_pinjam }}</td>
            <td>{{ $p->tanggal_peminjaman ? $p->tanggal_peminjaman->format('d M Y') : '-' }}</td>
            <td>{{ $p->tanggal_pengembalian ? $p->tanggal_pengembalian->format('d M Y') : '-' }}</td>
            <td>{{ ucfirst($p->status) }}</td>
            <td>
                @if(in_array($p->status, ['pending', 'peminjaman', 'pengembalian']))
                    <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Terima</button>
                    </form>
                    <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Tolak</button>
                    </form>
                
                 @elseif($p->status === 'disetujui') <!-- status sudah disetujui -->
                <a href="{{ route('admin.peminjaman.show', $p->id) }}">Lihat Detail</a>
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
