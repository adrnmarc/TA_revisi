<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

`<div style="text-align: center; margin-bottom: 20px;">
    <h1 style="margin: 0;">TK Mutiara Bogor</h1>
    <p style="margin: 0;">Jl. Vila Mutiara Bogor blok F2, RT.06/RW.12, Mekarwangi, Tanah Sareal, Kota Bogor, Jawa Barat 16168</p>
    <hr>
`</div>

    <h2 style="text-align: center;">Laporan Transaksi Keuangan</h2>
    <table border="1" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Nama Siswa</th>
                <th>Jenis Tagihan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatTransaksi as $tx)
            <tr>
                <td>{{ $tx->siswa->nama ?? '-' }}</td>
                <td>{{ $tx->tagihan->nama_tagihan ?? '-' }}</td>
                <td>Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}</td>
                <td>{{ $tx->status_tagihan }}</td>
                <td>{{ $tx->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>