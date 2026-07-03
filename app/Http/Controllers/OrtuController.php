<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailTagihan;
use Illuminate\Http\Request;

class OrtuController extends Controller
{
   public function dashboard()
{
    $siswa_id = session('siswa_id');

    $siswa = \App\Models\Siswa::find($siswa_id);

    // Logika transaksi
    $totalBayar = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)
        ->where('status_tagihan', 'Lunas')
        ->sum('jumlah_bayar');

    $jumlahTransaksi = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)->count();
    $riwayat = \App\Models\DetailTagihan::where('id_siswa', $siswa_id)->latest()->get();
    $pengumuman = \App\Models\Pengumuman::latest()->take(3)->get();

    // Kirim data ke view
    return view('ortu.dashboard', compact('totalBayar', 'jumlahTransaksi', 'riwayat', 'siswa', 'pengumuman'));
}

    public function tagihan()
    {
        $siswaId = session('siswa_id');
        $tagihans = \App\Models\DetailTagihan::where('id_siswa', $siswaId)->get();
        return view('ortu.tagihan', compact('tagihans'));
    }

    // Fungsi untuk menampilkan form upload
    public function formBayar($id)
    {
        $tagihan = DetailTagihan::findOrFail($id);
        return view('ortu.bayar', compact('tagihan'));
    }

   public function prosesBayar(Request $request, $id) 
{
    // Cek apakah file terkirim
    if (!$request->hasFile('bukti')) {
        die("Error: File tidak ditemukan di request!");
    }

    $tagihan = \App\Models\DetailTagihan::where('id_detail', $id)->first();
    
    if (!$tagihan) {
        die("Error: Data tagihan dengan ID $id tidak ditemukan di database!");
    }

    // Simpan file
    $path = $request->file('bukti')->store('bukti_bayar', 'public');
    
    // Update
    $tagihan->bukti_bayar = $path;
    $tagihan->status_tagihan = 'Menunggu Verifikasi';
    
    // Simpan
    if ($tagihan->save()) {
        return redirect('/ortu/tagihan')->with('sukses', 'Bukti berhasil dikirim!');
    } else {
        die("Error: Gagal menyimpan ke database!");
    }
}
        public function pengumuman()
    {
        // Mengambil semua pengumuman, diurutkan dari yang terbaru
        $pengumuman = \App\Models\Pengumuman::latest()->get();
        
        return view('ortu.pengumuman', compact('pengumuman'));
    }
}