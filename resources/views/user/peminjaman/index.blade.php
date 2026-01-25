<h1>Daftar Peminjaman</h1>
<table border="1">
    <tr>
        <th>Barang</th>
        <th>Jumlah</th>
        <th>Tanggal Pinjam</th>
        <th>Tanggal Kembali</th>
        <th>Status</th>
    </tr>
    @foreach($peminjamans as $pinjam)
    <tr>
        <td>{{ $pinjam->barang->nama }}</td>
        <td>{{ $pinjam->jumlah }}</td>
        <td>{{ $pinjam->tanggal_pinjam }}</td>
        <td>{{ $pinjam->tanggal_kembali }}</td>
        <td>{{ $pinjam->status }}</td>
    </tr>
    @endforeach
</table>
