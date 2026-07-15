<?php

namespace App\Http\Controllers;

use App\Models\DetailTagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class RiwayatPembayaranController extends Controller
{
   public function index()
{
    $siswa_id = session('siswa_id');

    // 1. Ambil semua tagihan siswa beserta data pembayarannya
    $semuaTagihan = DetailTagihan::where('id_siswa', $siswa_id)->get();

    // 2. Total Tagihan Keseluruhan
    $totalTagihan = $semuaTagihan->sum('jumlah_bayar');

    // 3. Hitung total yang sudah dibayar secara cerdas:
    // Kita looping setiap tagihan, lalu ambil nilai 'jumlah_diterima' dari relasi pembayaran
    $sudahDibayar = 0;
    foreach ($semuaTagihan as $tagihan) {
        // Jika statusnya 'Lunas', maka bayarnya adalah full sesuai jumlah tagihan
        if ($tagihan->status_tagihan == 'Lunas') {
            $sudahDibayar += $tagihan->jumlah_bayar;
        } else {
            // Jika belum lunas, hitung dari tabel pembayaran (cicilan)
            // Tambahkan status yang dianggap 'uang masuk'
            $sudahDibayar += $tagihan->pembayarans()
                ->whereIn('status', ['Diterima', 'Lunas', 'Menunggu', 'Verifikasi'])
                ->sum('jumlah_diterima');
        }
    }

    // 4. Sisa
    $belumDibayar = $totalTagihan - $sudahDibayar;

    $riwayat = $semuaTagihan->sortByDesc('created_at');

    return view('ortu.riwayat', compact('totalTagihan', 'sudahDibayar', 'belumDibayar', 'riwayat'));
}
}