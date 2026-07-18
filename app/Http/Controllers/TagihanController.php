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
        $tagihans = Tagihan::with([
            'siswa',
            'kategoriTagihan',
            'detailTagihan.pembayarans'
        ])
        ->latest()
        ->get();

        $daftarSiswa = Siswa::orderBy('nama')->get();
        $daftarKategori = KategoriTagihan::all(); 

        return view('admin.tagihan', compact(
            'tagihans',
            'daftarSiswa',
            'daftarKategori'
        ));
    }

    /**
     * Simpan tagihan baru (ADMIN) dengan Deteksi Cerdas Per Bulan
     */
    /**
     * Simpan tagihan baru (Satu Siswa atau Massal)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Hapus exists:siswas,nis agar value 'all' bisa lolos)
        $request->validate([
            'siswa_id' => 'required', 
            'id_kategori' => 'required|exists:kategori_tagihans,id', 
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
            'jatuh_tempo' => 'required|date', // Tambahan untuk Jatuh Tempo
        ]);

        $kategori = KategoriTagihan::findOrFail($request->id_kategori); 

        // Penamaan Tagihan Otomatis (Sama seperti sebelumnya)
        $namaTagihanAsli = $kategori->nama_kategori;
        $namaIuranLower = strtolower($namaTagihanAsli);
        $isProgram = str_contains($namaIuranLower, 'program') || str_contains($namaIuranLower, 'sekolah');

        $namaTagihan = $namaTagihanAsli;
        if (!$isProgram) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            if (!str_contains($namaTagihan, $bulanTahun)) {
                $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
            }
        }

        // 2. TENTUKAN TARGET: Satu Siswa atau Semua Siswa?
        if ($request->siswa_id === 'all') {
            // Ambil SEMUA data siswa dari database
            $siswas = Siswa::all();
            if ($siswas->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data siswa di database.');
            }
        } else {
            // Ambil SATU siswa sesuai NIS yang dipilih
            $siswas = Siswa::where('nis', $request->siswa_id)->get();
            if ($siswas->isEmpty()) {
                return redirect()->back()->with('error', 'Siswa tidak ditemukan.');
            }
        }

        $berhasil = 0;
        $gagal = 0;

        // 3. PROSES PEMBUATAN TAGIHAN (Looping)
        DB::transaction(function () use ($siswas, $kategori, $namaTagihan, $request, &$berhasil, &$gagal) {
            foreach ($siswas as $siswa) {
                // Cek apakah siswa ini SUDAH PUNYA tagihan dengan nama yang sama persis
                $tagihanTerdaftar = DetailTagihan::where('id_siswa', $siswa->id)
                                    ->where('nama_iuran', $namaTagihan)
                                    ->first();

                // Jika sudah punya, lewati (skip) agar tidak terjadi double tagihan
                if ($tagihanTerdaftar) {
                    $gagal++;
                    continue; 
                }

                // Jika belum punya, buatkan tagihan baru
                $tagihan = Tagihan::create([
                    'id_kategori' => $kategori->id, 
                    'id_siswa' => $siswa->id, 
                    'nis' => $siswa->nis,
                    'nama_tagihan' => $namaTagihan,
                    'nominal' => $request->nominal,
                    'jatuh_tempo' => $request->jatuh_tempo, // Gunakan input jatuh tempo
                    'status' => 'Belum Lunas',
                ]);

                DetailTagihan::create([
                    'id_tagihan' => $tagihan->id_tagihan,
                    'id_siswa' => $siswa->id,
                    'nama_iuran' => $namaTagihan,
                    'jumlah_bayar' => $request->nominal,
                    'status_tagihan' => 'Belum Lunas', 
                ]);

                $berhasil++;
            }
        });

        // 4. BERIKAN NOTIFIKASI HASIL
        if ($request->siswa_id === 'all') {
            $pesan = "Berhasil membuat tagihan untuk $berhasil siswa.";
            if ($gagal > 0) {
                $pesan .= " ($gagal siswa dilewati karena sudah memiliki tagihan ini).";
            }
            return redirect()->back()->with('sukses', $pesan);
        } else {
            if ($berhasil > 0) {
                return redirect()->back()->with('sukses', 'Tagihan berhasil ditambahkan.');
            } else {
                return redirect()->back()->with('error', 'Gagal! Siswa tersebut sudah memiliki tagihan ini.');
            }
        }
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
            $file->move(public_path('bukti_bayar'), $namaFileBukti);
            $namaFileBukti = 'bukti_bayar/' . $namaFileBukti; 
        }

        DB::transaction(function () use ($detail, $request, $namaFileBukti) {
            Pembayaran::create([
                'id_detail' => $detail->id_detail,
                'user_id' => Auth::id() ?? 1, 
                'tanggal_bayar' => now(),
                'jumlah_diterima' => $request->jumlah_bayar,
                'status' => 'Disetujui',
                'bukti_bayar' => $namaFileBukti,
            ]);

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
            'id_kategori' => 'required|exists:kategori_tagihans,id', 
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
            'jatuh_tempo' => 'required|date', // <--- TAMBAHAN VALIDASI JATUH TEMPO
            'status_tagihan' => 'required|in:Belum Lunas,Dicicil,Lunas',
        ]);

        $tagihan = Tagihan::findOrFail($id_tagihan);
        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();
        $kategori = KategoriTagihan::findOrFail($request->id_kategori); 

        // Terapkan logika penamaan bulan yang sama untuk proses update
        $namaTagihan = $kategori->nama_kategori;
        $namaIuranLower = strtolower($namaTagihan);
        $isProgram = str_contains($namaIuranLower, 'program') || str_contains($namaIuranLower, 'sekolah');

        if (!$isProgram) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            if (!str_contains($namaTagihan, $bulanTahun)) {
                $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
            }
        }

        // Mencegah duplikasi
        $duplikat = DetailTagihan::where('id_siswa', $siswa->id)
            ->where('nama_iuran', $namaTagihan)
            ->where('id_tagihan', '!=', $id_tagihan)
            ->exists();

        if ($duplikat) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan dengan nama "' . $namaTagihan . '".');
        }

        DB::transaction(function () use ($tagihan, $siswa, $kategori, $request, $namaTagihan) {
            $tagihan->update([
                'id_kategori' => $kategori->id, 
                'id_siswa' => $siswa->id, 
                'nis' => $siswa->nis,
                'nama_tagihan' => $namaTagihan,
                'nominal' => $request->nominal,
                'jatuh_tempo' => $request->jatuh_tempo, // <--- UBAH JADI MENYIMPAN JATUH TEMPO
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