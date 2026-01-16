@extends('layouts.admin') <!-- layout utama admin -->

@section('content')
    <h1>Daftar Kategori</h1>
    <ul>
        @foreach($kategori as $k)
            <li>{{ $k->nama_kategori }}</li>
        @endforeach
    </ul>
@endsection
