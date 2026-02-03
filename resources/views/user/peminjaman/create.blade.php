@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-4">Pilih Barang</h4>

    <form action="{{ route('user.peminjaman.form') }}" method="GET">
        <div class="row g-4">
            @foreach ($barang as $b)
                <div class="col-md-4">
                    <label class="w-100">
                        <input
                            type="radio"
                            name="barang_id"
                            value="{{ $b->id }}"
                            class="d-none"
                            {{ $b->stok <= 0 ? 'disabled' : '' }}
                            required
                        >

                        <div class="card barang-card {{ $b->stok <= 0 ? 'stok-habis' : '' }}">
                            @if ($b->image)
                                <img
                                    src="{{ asset('storage/' . $b->image) }}"
                                    class="card-img-top"
                                    style="height:180px; object-fit:cover"
                                >
                            @else
                                <div class="bg-light text-center py-5">
                                    No Image
                                </div>
                            @endif

                            <div class="card-body">
                                <h5>{{ $b->nama_barang }}</h5>
                                <small>
                                    Kategori: {{ $b->kategori?->kategori ?? '-' }}
                                </small>
                                <br>

                                @if ($b->stok > 0)
                                    <span class="badge bg-success">
                                        Stok {{ $b->stok }}
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Stok Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        <button class="btn btn-primary mt-4">
            Next →
        </button>
    </form>
</div>

<style>
.barang-card {
    cursor: pointer;
    transition: .2s;
    border: 2px solid transparent;
}
.barang-card:hover {
    transform: translateY(-4px);
}
input[type="radio"]:checked + .barang-card {
    border-color: #0d6efd;
}
.stok-habis {
    opacity: .5;
    pointer-events: none;
}
</style>
@endsection
