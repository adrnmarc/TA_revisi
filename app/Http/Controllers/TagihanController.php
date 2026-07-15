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
     * Simpan tagihan baru (ADMIN) dengan Deteksi Status Double Billing
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

        // 1. LOGIKA NAMA BULAN OTOMATIS UNTUK SPP
        $namaTagihan = $kategori->nama_kategori;
        if (str_contains(strtolower($namaTagihan), 'spp')) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
        }

        // 2. LOGIKA DETEKSI ERROR SPESIFIK (DOUBLE BILLING)
        $tagihanTerdaftar = DetailTagihan::where('id_siswa', $siswa->id)
                            ->where('nama_iuran', $namaTagihan)
                            ->first();

        if ($tagihanTerdaftar) {
            $pesan = $tagihanTerdaftar->status_tagihan === 'Lunas'
                ? 'Gagal! Tagihan "' . $namaTagihan . '" untuk ' . $siswa->nama . ' tidak bisa ditambahkan karena sudah dibayar/lunas.'
                : 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan aktif untuk "' . $namaTagihan . '".';

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
        $sisaSebelumnya = $detail->sisa_tagihan; // Menggunakan accessor dari model DetailTagihan

        if ($request->jumlah_bayar > $sisaSebelumnya) {
            return redirect()->back()->with('error', 'Jumlah bayar melebihi sisa tagihan! Sisa saat ini: Rp ' . number_format($sisaSebelumnya, 0, ',', '.'));
        }

        // Simpan Bukti Pembayaran jika diunggah
        $namaFileBukti = null;
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $namaFileBukti = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_bayar'), $namaFileBukti);
        }

        DB::transaction(function () use ($detail, $request, $namaFileBukti) {
            // 1. Simpan baris transaksi ke tabel pembayarans
            Pembayaran::create([
                'id_detail' => $detail->id_detail,
                'user_id' => Auth::id() ?? 1, // Jika tidak ter-auth, fallback ke ID 1 (Admin default)
                'tanggal_bayar' => now(),
                'jumlah_diterima' => $request->jumlah_bayar,
                'status' => 'Disetujui',
                'bukti_bayar' => $namaFileBukti,
            ]);

            // 2. Hitung total yang sudah dibayar (Termasuk pembayaran baru)
            $totalBayarMasuk = $detail->pembayarans()->sum('jumlah_diterima') + $request->jumlah_bayar;
            $sisaBaru = $detail->jumlah_bayar - $totalBayarMasuk;

            // 3. Tentukan status baru
            $statusFinal = 'Belum Lunas';
            if ($sisaBaru <= 0) {
                $statusFinal = 'Lunas';
            } elseif ($totalBayarMasuk > 0) {
                $statusFinal = 'Dicicil';
            }

            // 4. Update status ke DetailTagihan dan Tagihan induknya
            $detail->update(['status_tagihan' => $statusFinal]);
            $detail->tagihan->update(['status' => $statusFinal]);
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

        // Mencegah duplikasi nama iuran iuran yang sama untuk siswa yang sama
        $duplikat = DetailTagihan::where('id_siswa', $siswa->id)
            ->where('nama_iuran', $kategori->nama_kategori)
            ->where('id_tagihan', '!=', $id_tagihan)
            ->exists();

        if ($duplikat) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan dengan nama "' . $kategori->nama_kategori . '".');
        }

        DB::transaction(function () use ($tagihan, $siswa, $kategori, $request) {
            // Update tabel tagihans induk
            $tagihan->update([
                'kategori_tagihan_id' => $kategori->id,
                'nis' => $siswa->nis,
                'nama_tagihan' => $kategori->nama_kategori,
                'nominal' => $request->nominal,
                'jatuh_tempo' => $request->tanggal_tagihan,
                'status' => $request->status_tagihan,
            ]);

            // Update tabel detail_tagihans anak
            $detail = DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->first();
            if ($detail) {
                $detail->update([
                    'id_siswa' => $siswa->id,
                    'nama_iuran' => $kategori->nama_kategori,
                    'jumlah_bayar' => $request->nominal,
                    'status_tagihan' => $request->status_tagihan,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan (ADMIN) beserta Detail dan Pembayarannya sekaligus
     */
    public function destroy($id_tagihan)
    {
        $tagihan = Tagihan::findOrFail($id_tagihan);

        DB::transaction(function () use ($tagihan) {
            $detail = DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->first();
            
            if ($detail) {
                // Hapus riwayat pembayarannya terlebih dahulu
                $detail->pembayarans()->delete();
                // Hapus detail tagihan
                $detail->delete();
            }
            
            // Hapus tagihan induk
            $tagihan->delete();
        });

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan berhasil dihapus.');
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

        // Mengambil tagihan yang belum lunas (Belum Lunas & Dicicil)
        $tagihans = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', '!=', 'Lunas')
            ->with(['tagihan.kategoriTagihan', 'pembayarans'])
            ->get();

        // Mengambil tagihan yang sudah lunas
        $tagihanLunas = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', 'Lunas')
            ->with(['tagihan.kategoriTagihan', 'pembayarans'])
            ->get();

        return view('ortu.tagihan', compact('tagihans', 'tagihanLunas'));
    }
}