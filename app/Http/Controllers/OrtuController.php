<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // FIX: hanya tampilkan pengumuman yang tanggal terbitnya sudah tiba,
        // diurutkan berdasarkan tanggal terbit (bukan created_at)
        $pengumuman = \App\Models\Pengumuman::whereDate('tanggal', '<=', now())
            ->orderBy('tanggal', 'desc')
            ->take(3)
            ->get();

        return view('ortu.dashboard', compact('totalBayar', 'jumlahTransaksi', 'riwayat', 'siswa', 'pengumuman'));
    }

    /**
     * Menampilkan Daftar Tagihan Orang Tua
     */
    public function tagihan()
    {
        $siswaId = session('siswa_id');

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

            // Hitung sisa tagihan secara manual,
            // dan ABAIKAN pembayaran yang statusnya "Ditolak"
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
            'nominal_bayar' => 'required|array',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'tagihan_id.required' => 'Pilih minimal satu tagihan yang ingin dibayar!',
        ]);

        $siswaId = session('siswa_id');

        if (!$siswaId) {
            return redirect('/login-ortu')->with('gagal', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        // Simpan file bukti transfer
        $file = $request->file('bukti_bayar');
        $namaFileBukti = 'bukti_ortu_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('bukti_bayar'), $namaFileBukti);
        $pathBukti = 'bukti_bayar/' . $namaFileBukti;

        DB::transaction(function () use ($request, $pathBukti) {
            foreach ($request->tagihan_id as $id) {
                $detail = DetailTagihan::with('tagihan', 'pembayarans')->find($id);

                if ($detail) {
                    $nominalTotal = (float) ($request->nominal_bayar[$id] ?? 0);
                    $jumlahBulan = (int) ($request->jumlah_bulan[$id] ?? 1);
                    $isSpp = str_contains(strtolower($detail->nama_iuran), 'spp');

                    if ($nominalTotal > 0) {
                        $totalDiterimaSebelumnya = $detail->pembayarans
                            ->whereIn('status', ['Diterima', 'Lunas'])
                            ->sum('jumlah_diterima');

                        // ========================================================
                        // LOGIKA KHUSUS JIKA BAYAR SPP MULTI-BULAN
                        // ========================================================
                        if ($isSpp && $jumlahBulan > 1) {
                            
                            // 1. Lunasi tagihan bulan pertama
                            $sisaBulanPertama = $detail->jumlah_bayar - $totalDiterimaSebelumnya;
                            
                            Pembayaran::create([
                                'id_detail'       => $detail->id_detail,
                                'user_id'         => 1,
                                'tanggal_bayar'   => now(),
                                'jumlah_diterima' => $sisaBulanPertama, // Hanya pakai nominal 1 bulan
                                'bukti_bayar'     => $pathBukti,
                                'status'          => 'Menunggu Verifikasi',
                                'keterangan'      => 'Pembayaran via Portal Ortu (Multi-bulan)'
                            ]);

                            $detail->update([
                                'status_tagihan' => 'Menunggu Verifikasi',
                                'bukti_bayar'    => $pathBukti
                            ]);

                            $tanggalUpdate = Carbon::parse($detail->tagihan->jatuh_tempo)->day(5);
                            $hargaPerBulan = $detail->jumlah_bayar;

                            // 2. Kloning tagihan untuk bulan berikutnya DAN buat pembayarannya
                            for ($i = 1; $i < $jumlahBulan; $i++) {
                                $tanggalBaru = $tanggalUpdate->copy()->addMonths($i);
                                $namaBulanBaru = $tanggalBaru->translatedFormat('F Y');
                                $namaIuranBaru = 'Uang SPP / Bulan - ' . $namaBulanBaru;

                                // Kloning Induk
                                $tagihanBaru = Tagihan::create([
                                    'nis'          => $detail->tagihan->nis,
                                    'id_siswa'     => $detail->id_siswa,
                                    'nama_tagihan' => $namaIuranBaru,
                                    'jatuh_tempo'  => $tanggalBaru->format('Y-m-d'),
                                ]);

                                // Kloning Detail
                                $detailBaru = DetailTagihan::create([
                                    'id_tagihan'     => $tagihanBaru->id_tagihan,
                                    'id_siswa'       => $detail->id_siswa,
                                    'nama_iuran'     => $namaIuranBaru,
                                    'jumlah_bayar'   => $hargaPerBulan,
                                    'sisa_tagihan'   => $hargaPerBulan,
                                    'status_tagihan' => 'Menunggu Verifikasi',
                                    'bukti_bayar'    => $pathBukti
                                ]);

                                // BUAT RECORD PEMBAYARAN UNTUK KLONING INI
                                // Ini yang sebelumnya terlupa, agar Admin bisa memverifikasi bulan ini
                                Pembayaran::create([
                                    'id_detail'       => $detailBaru->id_detail,
                                    'user_id'         => 1, 
                                    'tanggal_bayar'   => now(),
                                    'jumlah_diterima' => $hargaPerBulan,
                                    'bukti_bayar'     => $pathBukti,
                                    'status'          => 'Menunggu Verifikasi',
                                    'keterangan'      => 'Pembayaran via Portal Ortu (Multi-bulan)'
                                ]);
                            }

                        } 
                        // ========================================================
                        // LOGIKA NORMAL (1 BULAN ATAU CICILAN NON-SPP)
                        // ========================================================
                        else {
                            $calonTotal = $totalDiterimaSebelumnya + $nominalTotal;
                            $akanLunas = $calonTotal >= $detail->jumlah_bayar;

                            Pembayaran::create([
                                'id_detail'       => $detail->id_detail,
                                'user_id'         => 1, 
                                'tanggal_bayar'   => now(),
                                'jumlah_diterima' => $nominalTotal,
                                'bukti_bayar'     => $pathBukti,
                                'status'          => 'Menunggu Verifikasi',
                                'keterangan'      => 'Pembayaran via Portal Ortu'
                            ]);

                            $tanggalUpdate = Carbon::parse($detail->tagihan->jatuh_tempo);
                            if ($isSpp) {
                                $tanggalUpdate = $tanggalUpdate->day(5);
                            }
                            $detail->tagihan->update(['jatuh_tempo' => $tanggalUpdate->format('Y-m-d')]);

                            $statusBaru = $akanLunas ? 'Menunggu Verifikasi' : ($totalDiterimaSebelumnya > 0 ? 'Mencicil' : 'Belum Lunas');

                            $detail->update([
                                'status_tagihan' => $statusBaru,
                                'bukti_bayar'    => $pathBukti
                            ]);
                        }
                    }
                }
            }
        });

        return back()->with('sukses', 'Bukti pembayaran berhasil dikirim!');
    }


    /**
     * Menampilkan Daftar Pengumuman
     */
    public function pengumuman()
    {
        // FIX: hanya tampilkan pengumuman yang tanggal terbitnya sudah tiba (hari ini atau sebelumnya),
        // dan urutkan berdasarkan tanggal terbit, bukan created_at
        $pengumuman = \App\Models\Pengumuman::whereDate('tanggal', '<=', now())
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('ortu.pengumuman', compact('pengumuman'));
    }
}