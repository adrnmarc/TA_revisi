<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\DetailTagihan;
use App\Models\Pembayaran;
use App\Models\KategoriTagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan di sisi ADMIN
     */
    public function index()
    {
        // Eager load relasi ke KategoriTagihan, Siswa, DetailTagihan, dan riwayat Pembayarannya
        $tagihans = Tagihan::with([
            'siswa',
            'kategoriTagihan',
            'detailTagihan.pembayarans'
        ])
        ->latest()
        ->get();

        $daftarSiswa = Siswa::orderBy('nama')->get();
        $daftarKategori = KategoriTagihan::all(); // Untuk dropdown form tambah tagihan

        return view('admin.tagihan', compact(
            'tagihans',
            'daftarSiswa',
            'daftarKategori'
        ));
    }

    /**
     * Simpan tagihan baru (ADMIN) dengan Deteksi Cerdas Per Bulan
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis',
            'kategori_tagihan_id' => 'required|exists:kategori_tagihans,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
        ]);

        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();
        $kategori = KategoriTagihan::findOrFail($request->kategori_tagihan_id);

        // 1. FORMAT NAMA TAGIHAN (Hanya SPP yang digabungkan dengan nama bulan secara visual)
        $namaTagihan = $kategori->nama_kategori;
        if (str_contains(strtolower($namaTagihan), 'spp')) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
        }

        // 2. LOGIKA DETEKSI DOUBLE BILLING BERDASARKAN KONDISI KATEGORI
        $tanggalInput = Carbon::parse($request->tanggal_tagihan);
        
        // Query dasar pencarian tagihan dengan nama iuran yang sama pada siswa tersebut
        $queryCek = DetailTagihan::where('id_siswa', $siswa->id)
                                 ->where('nama_iuran', $namaTagihan);

        // KONDISI: Jika BUKAN "Uang Program", validasi dikunci per bulan & tahun inputan
        if (!str_contains(strtolower($kategori->nama_kategori), 'program')) {
            $queryCek->whereHas('tagihan', function ($query) use ($tanggalInput) {
                $query->whereMonth('jatuh_tempo', $tanggalInput->month)
                      ->whereYear('jatuh_tempo', $tanggalInput->year);
            });
        }

        $tagihanTerdaftar = $queryCek->first();

        if ($tagihanTerdaftar) {
            // Jika kategori yang melanggar adalah Uang Program
            if (str_contains(strtolower($kategori->nama_kategori), 'program')) {
                $pesan = 'Gagal! Tagihan "' . $namaTagihan . '" untuk ' . $siswa->nama . ' hanya boleh dibayarkan sekali saja dan sudah terdaftar sebelumnya.';
            } else {
                // Jika kategori bulanan biasa (MMP, Ekskul, POMG, dll)
                $pesan = $tagihanTerdaftar->status_tagihan === 'Lunas'
                    ? 'Gagal! Tagihan "' . $namaTagihan . '" untuk ' . $siswa->nama . ' tidak bisa ditambahkan karena sudah dibayar/lunas pada bulan ini.'
                    : 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan aktif untuk "' . $namaTagihan . '" pada bulan ini.';
            }

            return redirect()->back()->withInput()->with('error', $pesan);
        }

        // 3. SIMPAN DATA DENGAN TRANSACTION
        DB::transaction(function () use ($siswa, $kategori, $namaTagihan, $request) {
            $tagihan = Tagihan::create([
                'kategori_tagihan_id' => $kategori->id,
                'nis' => $siswa->nis,
                'nama_tagihan' => $namaTagihan, 
                'nominal' => $request->nominal,
                'jatuh_tempo' => $request->tanggal_tagihan,
                'status' => 'Belum Lunas',
            ]);

            DetailTagihan::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'id_siswa' => $siswa->id,
                'nama_iuran' => $namaTagihan, 
                'jumlah_bayar' => $request->nominal,
                'status_tagihan' => 'Belum Lunas', 
            ]);
        });

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan ' . $namaTagihan . ' berhasil ditambahkan.');
    }

    /**
     * Memproses Pembayaran / Input Cicilan Siswa dari sisi Admin
     */
    public function bayarCicilan(Request $request, $id_detail)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1000',
            'bukti_bayar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $detail = DetailTagihan::with('tagihan.kategoriTagihan')->findOrFail($id_detail);
        $sisaSebelumnya = $detail->sisa_tagihan; 

        if ($request->jumlah_bayar > $sisaSebelumnya) {
            return redirect()->back()->with('error', 'Jumlah bayar melebihi sisa tagihan! Sisa saat ini: Rp ' . number_format($sisaSebelumnya, 0, ',', '.'));
        }

        $namaFileBukti = null;
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $namaFileBukti = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_bayar'), $namaFileBukti);
        }

        DB::transaction(function () use ($detail, $request, $namaFileBukti) {
            $totalBayarMasuk = $detail->pembayarans()->sum('jumlah_diterima') + $request->jumlah_bayar;
            $sisaBaru = $detail->jumlah_bayar - $totalBayarMasuk;

            $statusFinal = 'Belum Lunas';
            if ($sisaBaru <= 0) {
                $statusFinal = 'Lunas';
            } elseif ($totalBayarMasuk > 0) {
                $statusFinal = 'Dicicil';
            }

            $detail->update(['status_tagihan' => $statusFinal]);
            $detail->tagihan->update(['status' => $statusFinal]);

            Pembayaran::create([
                'id_detail' => $detail->id_detail,
                'user_id' => Auth::id() ?? 1, 
                'tanggal_bayar' => now(),
                'jumlah_diterima' => $request->jumlah_bayar,
                'status' => 'Disetujui',
                'bukti_bayar' => $namaFileBukti,
            ]);
        });

        return redirect()->back()->with('sukses', 'Pembayaran berhasil dicatat!');
    }

    /**
     * Update data tagihan (ADMIN)
     */
    public function update(Request $request, $id_tagihan)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis',
            'kategori_tagihan_id' => 'required|exists:kategori_tagihans,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
            'status_tagihan' => 'required|in:Belum Lunas,Dicicil,Lunas',
        ]);

        $tagihan = Tagihan::findOrFail($id_tagihan);
        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();
        $kategori = KategoriTagihan::findOrFail($request->kategori_tagihan_id);

        $namaTagihan = $kategori->nama_kategori;
        if (str_contains(strtolower($namaTagihan), 'spp')) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
        }

        $tanggalInput = Carbon::parse($request->tanggal_tagihan);
        $queryDuplikat = DetailTagihan::where('id_siswa', $siswa->id)
            ->where('nama_iuran', $namaTagihan)
            ->where('id_tagihan', '!=', $id_tagihan);

        if (!str_contains(strtolower($kategori->nama_kategori), 'program')) {
            $queryDuplikat->whereHas('tagihan', function ($query) use ($tanggalInput) {
                $query->whereMonth('jatuh_tempo', $tanggalInput->month)
                      ->whereYear('jatuh_tempo', $tanggalInput->year);
            });
        }

        if ($queryDuplikat->exists()) {
            $pesanError = str_contains(strtolower($kategori->nama_kategori), 'program')
                ? 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan "' . $namaTagihan . '" sebelumnya.'
                : 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan "' . $namaTagihan . '" pada bulan yang dipilih.';

            return redirect()->back()->withInput()->with('error', $pesanError);
        }

        DB::transaction(function () use ($tagihan, $siswa, $kategori, $namaTagihan, $request) {
            $tagihan->update([
                'kategori_tagihan_id' => $kategori->id,
                'nis' => $siswa->nis,
                'nama_tagihan' => $namaTagihan,
                'nominal' => $request->nominal,
                'jatuh_tempo' => $request->tanggal_tagihan,
                'status' => $request->status_tagihan,
            ]);

            $detail = DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->first();
            if ($detail) {
                $detail->update([
                    'id_siswa' => $siswa->id,
                    'nama_iuran' => $namaTagihan,
                    'jumlah_bayar' => $request->nominal,
                    'status_tagihan' => $request->status_tagihan,
                ]);
            }
        });

        return redirect()->back()->with('sukses', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan (ADMIN)
     */
    public function destroy($id_tagihan)
    {
        $tagihan = Tagihan::findOrFail($id_tagihan);

        DB::transaction(function () use ($tagihan) {
            $detail = DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->first();
            if ($detail) {
                $detail->pembayarans()->delete();
                $detail->delete();
            }
            $tagihan->delete();
        });

        return redirect()->back()->with('sukses', 'Tagihan berhasil dihapus.');
    }

    /**
     * FUNGSI KHUSUS PORTAL ORANG TUA (ORTU)
     */
    public function ortuIndex()
    {
        $user = Auth::user();
        $siswaId = null;

        if ($user) {
            $siswaId = session('siswa_id') 
                ?? (method_exists($user, 'siswa') && $user->siswa ? $user->siswa->id : null)
                ?? $user->siswa_id;

            if (!$siswaId) {
                $siswa = Siswa::where('email', $user->email)
                    ->orWhere('id', $user->id)
                    ->first();
                $siswaId = $siswa ? $siswa->id : null;
            }
        }

        $siswaId = $siswaId ?? Siswa::value('id'); 

        $tagihans = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', '!=', 'Lunas')
            ->with(['tagihan.kategoriTagihan', 'pembayarans'])
            ->get();

        $tagihanLunas = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', 'Lunas')
            ->with(['tagihan.kategoriTagihan', 'pembayarans'])
            ->get();

        return view('ortu.tagihan', compact('tagihans', 'tagihanLunas'));
    }
}