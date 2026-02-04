@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-4">Daftar Peminjaman</h4>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th class="text-center" width="240">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $index => $p)
                        @php $status = strtolower($p->status); @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>{{ $p->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $p->jumlah_pinjam }}</td>
                            <td>{{ $p->tanggal_peminjaman?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $p->tanggal_pengembalian?->format('d M Y') ?? '-' }}</td>
                            <td>
                                @if ($status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif ($status === 'pengembalian')
                                    <span class="badge bg-info text-dark">Pengembalian</span>
                                @elseif ($status === 'dikembalikan')
                                    <span class="badge bg-secondary">Dikembalikan</span>
                                @elseif ($status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">

                                {{-- ACC PEMINJAMAN --}}
                                @if ($status === 'pending')
                                    <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Terima</button>
                                    </form>

                                    <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">Tolak</button>
                                    </form>

                                {{-- ACC PENGEMBALIAN --}}
                                @elseif ($status === 'pengembalian')
                                    <form action="{{ route('admin.peminjaman.return.approve', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-primary">ACC Pengembalian</button>
                                    </form>

                                    <form action="{{ route('admin.peminjaman.return.reject', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">Tolak</button>
                                    </form>

                                {{-- SUDAH SELESAI --}}
                                @else
                                    <a href="{{ route('admin.peminjaman.show', $p->id) }}" class="btn btn-sm btn-info">
                                        Detail
                                    </a>
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
