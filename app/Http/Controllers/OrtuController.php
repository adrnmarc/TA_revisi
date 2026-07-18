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
    /**
     * Menampilkan Dashboard Orang Tua
     */
    public function dashboard()
    {
        $siswa_id = session('siswa_id');
        $siswa = Siswa::find($siswa_id);

        // Jika session habis, kembalikan ke halaman login
        if (!$siswa) {
            return redirect('/login-ortu')->with('gagal', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

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
        $jumlahTransaksi = Pembayaran::whereHas('detailTagihan', function($query) use ($siswa_id) {
            $query->where('id_siswa', $siswa_id);
        })
        ->whereIn('status', ['Diterima', 'Lunas', 'Menunggu', 'Verifikasi'])
        ->count();

        $riwayat = DetailTagihan::where('id_siswa', $siswa_id)->latest()->get();
        $pengumuman = \App\Models\Pengumuman::latest()->take(3)->get();

        return view('ortu.dashboard', compact('totalBayar', 'jumlahTransaksi', 'riwayat', 'siswa', 'pengumuman'));
    }

    /**
     * Menampilkan Daftar Tagihan Orang Tua
     */
    public function tagihan()
    {
        $siswaId = session('siswa_id');
        
        // ==============================================================
        // PERBAIKAN 1: Hapus ->withSum() dari sini
        // ==============================================================
        $tagihans = DetailTagihan::where('id_siswa', $siswaId)
            ->with(['tagihan', 'pembayarans'])
            ->get();

        foreach ($tagihans as $tagihan) {
            // LOGIKA KOREKSI OTOMATIS:
            // Jika nama iuran mengandung kata 'spp' DAN tanggalnya BUKAN tanggal 5
            if (str_contains(strtolower($tagihan->nama_iuran), 'spp')) {
                $tanggalJatuhTempo = Carbon::parse($tagihan->tagihan->jatuh_tempo);
                
                if ($tanggalJatuhTempo->day !== 5) {
                    $tanggalBaru = $tanggalJatuhTempo->day(5)->format('Y-m-d');
                    
                    // Update ke database agar permanen
                    $tagihan->tagihan->update(['jatuh_tempo' => $tanggalBaru]);
                }
            }

            // ==============================================================
            // PERBAIKAN 2: Hitung sisa tagihan secara manual,
            // dan ABAIKAN pembayaran yang statusnya "Ditolak"
            // ==============================================================
            $totalTerbayar = $tagihan->pembayarans
                                     ->where('status', '!=', 'Ditolak')
                                     ->sum('jumlah_diterima');
                                     
            $tagihan->sisa_tagihan = $tagihan->jumlah_bayar - $totalTerbayar;
        }

        return view('ortu.tagihan', compact('tagihans'));
    }

    /**
     * Memproses Unggah Pembayaran (Bayar Banyak / Tunggal)
     */
    public function bayarBanyak(Request $request)
    {
        $request->validate([
            'tagihan_id'  => 'required|array|min:1',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'tagihan_id.required' => 'Pilih minimal satu tagihan yang ingin dibayar!',
        ]);

        // Ambil ID siswa dari session kustom login orang tua Anda
        $siswaId = session('siswa_id');

        // Pengaman jika session login tiba-tiba habis saat mengirim form
        if (!$siswaId) {
            return redirect('/login-ortu')->with('gagal', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        // ====================================================================
        // PERBAIKAN DI SINI: Menyimpan langsung ke public/bukti_bayar tanpa storage
        // ====================================================================
        $file = $request->file('bukti_bayar');
        $namaFileBukti = 'bukti_ortu_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Pindahkan langsung ke folder public fisik
        $file->move(public_path('bukti_bayar'), $namaFileBukti);
        
        // Bentuk path string untuk disimpan ke database
        $pathBukti = 'bukti_bayar/' . $namaFileBukti;
        // ====================================================================

        foreach ($request->tagihan_id as $id) {
            $detail = DetailTagihan::with('tagihan')->find($id);
            
            if ($detail) {
                // Perhitungan sisa tagihan agar nominal yang masuk ke 'jumlah_diterima' akurat
                $totalTerbayar = Pembayaran::where('id_detail', $detail->id_detail)
                    ->whereIn('status', ['Diterima', 'Lunas'])
                    ->sum('jumlah_diterima');
                $sisaBayar = $detail->jumlah_bayar - $totalTerbayar;

                // 1. REKAM HISTORI DI TABEL PEMBAYARAN (Sesuai LRS)
                Pembayaran::create([
                    'id_detail'       => $detail->id_detail,
                    'user_id'         => 1, // Menggunakan ID Admin/Sistem (1) untuk menghindari error Foreign Key 1452
                    'tanggal_bayar'   => now(),
                    'jumlah_diterima' => $sisaBayar, 
                    'bukti_bayar'     => $pathBukti,            
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
                    'bukti_bayar'    => $pathBukti // Disimpan sementara agar Admin dapat melihat fotonya di halaman verifikasi
                ]);

                // 3. LOGIKA BAYAR DI MUKA (KLONING TAGIHAN)
                $jumlahBulan = $request->jumlah_bulan[$id] ?? 1;

                if ($jumlahBulan > 1 && str_contains(strtolower($detail->nama_iuran), 'spp')) {
                    for ($i = 1; $i < $jumlahBulan; $i++) {
                        $tanggalBaru = $tanggalUpdate->copy()->addMonths($i)->day(5);
                        $namaBulanBaru = $tanggalBaru->translatedFormat('F Y');
                        $namaIuranBaru = 'Uang SPP / Bulan - ' . $namaBulanBaru;

                        $tagihanBaru = Tagihan::create([
                            'nis'          => $detail->tagihan->nis,
                            'nama_tagihan' => $namaIuranBaru,
                            'jatuh_tempo'  => $tanggalBaru->format('Y-m-d'),
                        ]);

                        DetailTagihan::create([
                            'id_tagihan'     => $tagihanBaru->id_tagihan,
                            'id_siswa'       => $detail->id_siswa,
                            'nama_iuran'     => $namaIuranBaru,
                            'jumlah_bayar'   => $detail->jumlah_bayar,
                            'sisa_tagihan'   => $detail->jumlah_bayar,
                            'status_tagihan' => 'Menunggu Verifikasi',
                            'bukti_bayar'    => $pathBukti 
                        ]);
                    }
                }
            }
        }

        return back()->with('sukses', 'Bukti pembayaran berhasil dikirim!');
    }

    /**
     * Menampilkan Daftar Pengumuman
     */
    public function pengumuman()
    {
        $pengumuman = \App\Models\Pengumuman::latest()->get();
        return view('ortu.pengumuman', compact('pengumuman'));
    }
}