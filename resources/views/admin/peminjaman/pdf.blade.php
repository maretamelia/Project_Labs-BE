<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Peminjaman</title>
    <style>
        /* CSS untuk meniru tampilan FE */
        body { 
            font-family: sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; color: #4A90E2; }
        .header p { margin: 5px 0 0; color: #666; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        /* Styling Header Tabel */
        th { 
            background-color: #4A90E2; 
            color: white; 
            padding: 10px 5px; 
            border: 1px solid #ddd;
            text-transform: uppercase;
        }
        
        /* Styling Baris Tabel */
        td { 
            padding: 8px 5px; 
            border: 1px solid #ddd; 
            text-align: center; 
            vertical-align: middle;
        }

        /* Baris Selang-seling */
        tr:nth-child(even) { background-color: #f9f9f9; }

        /* Align khusus teks panjang */
        .text-left { text-align: left; padding-left: 8px; }

        /* Warna Status sesuai FE */
        .status-selesai { color: #28a745; font-weight: bold; }
        .status-ditolak { color: #dc3545; font-weight: bold; }
        .status-terlambat { color: #fd7e14; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN RIWAYAT PEMINJAMAN LAB</h2>
        <p>Dicetak pada: {{ date('d-m-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30px">No</th>
                <th>Nama Peminjam</th>
                <th>Nama Barang</th>
                <th width="50px">Jumlah</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="text-left">{{ $row->user->name ?? '-' }}</td>
                <td class="text-left">{{ $row->barang->nama_barang ?? '-' }}</td>
                <td>{{ $row->jumlah }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_peminjaman)->format('d/m/Y') }}</td>
                <td>
                    {{ $row->tanggal_pengembalian_selesai 
                        ? \Carbon\Carbon::parse($row->tanggal_pengembalian_selesai)->format('d/m/Y') 
                        : '-' 
                    }}
                </td>
                <td class="status-{{ strtolower($row->status) }}">
                    {{ ucfirst($row->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>