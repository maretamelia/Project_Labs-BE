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
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
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
                        <th class="text-center" width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamans as $peminjaman)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $peminjaman->barang->nama ?? '-' }}</td>
                            <td>{{ $peminjaman->barang->kategori->nama ?? '-' }}</td>
                            <td>{{ $peminjaman->jumlah }}</td>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</td>
                            <td>
                                {{ $peminjaman->tanggal_kembali
                                    ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y')
                                    : '-' }}
                            </td>
                            <td>
                                @php $status = $peminjaman->status; @endphp
                                @if ($status === 'menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
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
                                @if ($status === 'menunggu')
                                    <a href="{{ route('user.peminjaman.edit', $peminjaman->id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('user.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
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
