<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>TK Mutiara Bogor</h1>
        <p>Jl. Vila Mutiara Bogor blok F2, Mekarwangi, Tanah Sareal, Kota Bogor, Jawa Barat 16168</p>
        <hr>
    </div>

    <h2 style="text-align: center;">Laporan Transaksi Keuangan</h2>
    <p style="text-align: center;">Periode: {{ request('start_date') ?: 'Semua' }} s/d {{ request('end_date') ?: 'Semua' }}</p>

    <table border="1" width="100%">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 20%;">Nama Siswa</th>
                <th style="width: 30%;">Jenis Tagihan</th>
                <th style="width: 15%;">Nominal</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 20%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatTransaksi as $tx)
                <tr>
                    <td style="padding: 8px;">{{ $tx->siswa->nama ?? '-' }}</td>
                    <td style="padding: 8px;">{{ $tx->nama_iuran }}</td>
                    <td style="padding: 8px;">Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}</td>
                    <td style="padding: 8px;">{{ $tx->status_tagihan }}</td>
                    {{-- Format disamakan dengan tampilan Web Admin --}}
                    <td style="padding: 8px;">
                        @if($tx->tagihan && $tx->tagihan->jatuh_tempo)
                            {{ \Carbon\Carbon::parse($tx->tagihan->jatuh_tempo)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px; float: right; width: 200px; text-align: center;">
        <p>Bogor, {{ date('d-m-Y') }}</p>
        <br><br><br>
        <p>( ____________________ )</p>
        <p>Admin Keuangan</p>
    </div>

</body>
</html>