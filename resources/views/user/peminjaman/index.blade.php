@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Peminjaman Saya</h4>
        <a href="{{ route('user.peminjaman.create') }}" class="btn btn-primary">
            + Ajukan Peminjaman
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th class="text-center" width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamans as $peminjaman)
                        @php $status = strtolower($peminjaman->status); @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <!-- Ganti nama field sesuai database -->
                            <td>{{ $peminjaman->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $peminjaman->barang->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $peminjaman->jumlah_pinjam }}</td>
                            <td>{{ $peminjaman->tanggal_peminjaman->format('d M Y') }}</td>
                            <td>
                                {{ $peminjaman->tanggal_pengembalian
                                    ? $peminjaman->tanggal_pengembalian->format('d M Y')
                                    : '-' }}
                            </td>
                            <td>
                                @if ($status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif ($status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @elseif ($status === 'dikembalikan')
                                    <span class="badge bg-secondary">Dikembalikan</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($status === 'pending')
                                    <a href="{{ route('user.peminjaman.edit', $peminjaman->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                    <form action="{{ route('user.peminjaman.destroy', $peminjaman->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>

                                @elseif ($status === 'disetujui')
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $tanggalKembali = $peminjaman->tanggal_pengembalian;
                                    @endphp
                                    <form action="{{ route('user.peminjaman.return', $peminjaman->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" {{ $now->lt($tanggalKembali) ? 'disabled' : '' }}>
                                            Pengembalian
                                        </button>
                                    </form>

                                @elseif (in_array($status, ['ditolak', 'dikembalikan']))
                                    <a href="{{ route('user.peminjaman.show', $peminjaman->id) }}" class="btn btn-sm btn-info">Detail</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data peminjaman
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
