@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-4">Riwayat Peminjaman</h4>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->user->name ?? '-' }}</td>
                <td>{{ $p->barang->nama_barang ?? '-' }}</td>
                <td>{{ $p->jumlah_pinjam }}</td>
                <td>{{ $p->tanggal_peminjaman ? $p->tanggal_peminjaman->format('d M Y') : '-' }}</td>
                <td>{{ $p->tanggal_pengembalian ? $p->tanggal_pengembalian->format('d M Y') : '-' }}</td>
                <td>
                    @if($p->status === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @elseif($p->status === 'dikembalikan')
                        <span class="badge bg-secondary">Dikembalikan</span>
                    @else
                        <span class="badge bg-light text-dark">{{ ucfirst($p->status) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat peminjaman</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
