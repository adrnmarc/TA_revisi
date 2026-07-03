<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;

class RiwayatPembayaranController extends Controller
{
   public function index()
{
    $siswa_id = session('siswa_id');

    // 1. Total keseluruhan tagihan yang pernah dibebankan kepada siswa
    $totalTagihan = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)
        ->sum('jumlah_bayar');

    // 2. Total yang statusnya SUDAH LUNAS
    $sudahDibayar = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)
        ->where('status_tagihan', 'Lunas')
        ->sum('jumlah_bayar');

    // 3. Logika untuk Belum Dibayar
    $belumDibayar = $totalTagihan - $sudahDibayar;

    $riwayat = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)
        ->latest()
        ->get();

    return view('ortu.riwayat', compact('totalTagihan', 'sudahDibayar', 'belumDibayar', 'riwayat'));
}
}