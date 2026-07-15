<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrtuController extends Controller
{
   public function dashboard()
{
    $siswa_id = session('siswa_id');
    $siswa = Siswa::find($siswa_id);

    // 1. Ambil semua tagihan siswa
    $semuaTagihan = DetailTagihan::where('id_siswa', $siswa_id)->get();

    // 2. Hitung total yang sudah dibayar (Logika sama dengan Riwayat)
    $totalBayar = 0;
    foreach ($semuaTagihan as $tagihan) {
        if ($tagihan->status_tagihan == 'Lunas') {
            $totalBayar += $tagihan->jumlah_bayar;
        } else {
            // Hitung cicilan dari relasi pembayaran
            $totalBayar += $tagihan->pembayarans()
                ->whereIn('status', ['Diterima', 'Lunas', 'Menunggu', 'Verifikasi'])
                ->sum('jumlah_diterima');
        }
    }

    // 3. Jumlah transaksi (Menghitung record di tabel Pembayaran milik siswa ini)
    // Kita pakai whereHas untuk mencari pembayaran berdasarkan id_siswa di detail_tagihan
    $jumlahTransaksi = \App\Models\Pembayaran::whereHas('detailTagihan', function($query) use ($siswa_id) {
        $query->where('id_siswa', $siswa_id);
    })
    ->whereIn('status', ['Diterima', 'Lunas', 'Menunggu', 'Verifikasi'])
    ->count();

    $riwayat = DetailTagihan::where('id_siswa', $siswa_id)->latest()->get();
    $pengumuman = \App\Models\Pengumuman::latest()->take(3)->get();

    return view('ortu.dashboard', compact('totalBayar', 'jumlahTransaksi', 'riwayat', 'siswa', 'pengumuman'));
}
   public function tagihan()
    {
        $siswaId = session('siswa_id');
        
        $tagihans = DetailTagihan::where('id_siswa', $siswaId)
            ->with(['tagihan', 'pembayarans'])
            ->withSum('pembayarans', 'jumlah_diterima')
            ->get();

        foreach ($tagihans as $tagihan) {
            // LOGIKA KOREKSI OTOMATIS:
            // Jika nama iuran mengandung kata 'spp' DAN tanggalnya BUKAN tanggal 5
            if (str_contains(strtolower($tagihan->nama_iuran), 'spp')) {
                $tanggalJatuhTempo = \Carbon\Carbon::parse($tagihan->tagihan->jatuh_tempo);
                
                if ($tanggalJatuhTempo->day !== 5) {
                    $tanggalBaru = $tanggalJatuhTempo->day(5)->format('Y-m-d');
                    
                    // Update ke database agar permanen
                    $tagihan->tagihan->update(['jatuh_tempo' => $tanggalBaru]);
                }
            }

            // Hitung sisa tagihan
            $totalTerbayar = $tagihan->pembayarans_sum_jumlah_diterima ?? 0;
            $tagihan->sisa_tagihan = $tagihan->jumlah_bayar - $totalTerbayar;
        }

        return view('ortu.tagihan', compact('tagihans'));
    }
   public function bayarBanyak(Request $request)
    {
        $request->validate([
            'tagihan_id'  => 'required|array|min:1',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'tagihan_id.required' => 'Pilih minimal satu tagihan yang ingin dibayar!',
        ]);

        $pathBukti = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

        foreach ($request->tagihan_id as $id) {
            $detail = DetailTagihan::with('tagihan')->find($id);
            
            if ($detail) {
                // 1. REKAM HISTORI DI TABEL PEMBAYARAN (Sesuai LRS)
                \App\Models\Pembayaran::create([
                    'id_detail'       => $detail->id_detail,
                    'user_id'         => Auth::id(),
                    'tanggal_bayar'   => now(),
                    'jumlah_diterima' => $detail->sisa_tagihan, // Mengambil sisa yang harus dibayar
                    'bukti_bayar'     => $pathBukti,            // Foto tersimpan di sini
                    'status'          => 'Menunggu Verifikasi',
                    'keterangan'      => 'Pembayaran via Portal Ortu'
                ]);

                // 2. UPDATE STATUS TAGIHAN
                $tanggalUpdate = Carbon::parse($detail->tagihan->jatuh_tempo);
                if (str_contains(strtolower($detail->nama_iuran), 'spp')) {
                    $tanggalUpdate = $tanggalUpdate->day(5);
                }
                
                $detail->tagihan->update(['jatuh_tempo' => $tanggalUpdate->format('Y-m-d')]);

                $detail->update([
                    'status_tagihan' => 'Menunggu Verifikasi',
                    'bukti_bayar'    => $pathBukti // Masih disimpan di sini untuk mempermudah Admin melihat di tabel verifikasi
                ]);

                // 3. LOGIKA BAYAR DI MUKA (KLONING TAGIHAN)
                $jumlahBulan = $request->jumlah_bulan[$id] ?? 1;

                if ($jumlahBulan > 1 && str_contains(strtolower($detail->nama_iuran), 'spp')) {
                    for ($i = 1; $i < $jumlahBulan; $i++) {
                        $tanggalBaru = $tanggalUpdate->copy()->addMonths($i)->day(5);
                        $namaBulanBaru = $tanggalBaru->translatedFormat('F Y');
                        $namaIuranBaru = 'Uang SPP / Bulan - ' . $namaBulanBaru;

                        $tagihanBaru = Tagihan::create([
                            'nis' => $detail->tagihan->nis,
                            'nama_tagihan' => $namaIuranBaru,
                            'jatuh_tempo' => $tanggalBaru->format('Y-m-d'),
                        ]);

                        DetailTagihan::create([
                            'id_tagihan' => $tagihanBaru->id_tagihan,
                            'id_siswa' => $detail->id_siswa,
                            'nama_iuran' => $namaIuranBaru,
                            'jumlah_bayar' => $detail->jumlah_bayar,
                            'sisa_tagihan' => $detail->jumlah_bayar,
                            'status_tagihan' => 'Menunggu Verifikasi',
                            'bukti_bayar' => $pathBukti 
                        ]);
                    }
                }
            }
        }

        return back()->with('sukses', 'Bukti pembayaran berhasil dikirim!');
    }

    public function pengumuman()
    {
        $pengumuman = \App\Models\Pengumuman::latest()->get();
        return view('ortu.pengumuman', compact('pengumuman'));
    }
}