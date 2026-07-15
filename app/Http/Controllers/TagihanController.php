<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\DetailTagihan;
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
            'detailTagihan'
        ])
        ->latest()
        ->get();

        $daftarSiswa = Siswa::orderBy('nama')->get();

        return view('admin.tagihan', compact(
            'tagihans',
            'daftarSiswa'
        ));
    }

    /**
     * Simpan tagihan baru (ADMIN) dengan Deteksi Status Double Billing
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
        ]);

        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();

        // 1. LOGIKA NAMA BULAN OTOMATIS
        $namaTagihan = $request->jenis_tagihan;
        if (str_contains(strtolower($namaTagihan), 'spp')) {
            $bulanTahun = Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
        }

        // 2. LOGIKA DETEKSI ERROR SPESIFIK (SUDAH LUNAS / BELUM LUNAS)
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
        DB::transaction(function () use ($siswa, $namaTagihan, $request) {
            $tagihan = Tagihan::create([
                'nis' => $siswa->nis,
                'nama_tagihan' => $namaTagihan, 
                'jatuh_tempo' => $request->tanggal_tagihan, 
            ]);

            DetailTagihan::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'id_siswa' => $siswa->id,
                'nama_iuran' => $namaTagihan, 
                'jumlah_bayar' => $request->nominal,
                'sisa_tagihan' => $request->nominal,
                'status_tagihan' => 'Belum Lunas', 
            ]);
        });

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan ' . $namaTagihan . ' berhasil ditambahkan.');
    }

    /**
     * Update tagihan (ADMIN) dengan Fitur Pengaturan Cicilan
     */
    public function update(Request $request, $id_tagihan)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
            'status_tagihan' => 'required|in:Belum Lunas,Dicicil,Lunas',
            'cicilan_ke' => 'required_if:status_tagihan,Dicicil|nullable|in:1,2,3',
        ], [
            'cicilan_ke.required_if' => 'Tahap cicilan wajib diisi jika status pembayaran diatur ke Dicicil.',
            'cicilan_ke.in' => 'Batas pengisian cicilan maksimal hanya sampai 3x.',
        ]);

        $tagihan = Tagihan::findOrFail($id_tagihan);
        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();

        // Mencegah duplikasi nama iuran yang sama untuk siswa yang sama (kecuali milik tagihan ini sendiri)
        $duplikat = DetailTagihan::where('id_siswa', $siswa->id)
            ->where('nama_iuran', $request->jenis_tagihan)
            ->where('id_tagihan', '!=', $id_tagihan)
            ->exists();

        if ($duplikat) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan dengan nama "' . $request->jenis_tagihan . '".');
        }

        // UPDATE DATA DENGAN TRANSACTION
        DB::transaction(function () use ($tagihan, $siswa, $request) {
            $tagihan->update([
                'nis' => $siswa->nis,
                'nama_tagihan' => $request->jenis_tagihan,
                'jatuh_tempo' => $request->tanggal_tagihan,
            ]);

            $detail = DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->first();

            if ($detail) {
                $detail->update([
                    'id_siswa' => $siswa->id,
                    'nama_iuran' => $request->jenis_tagihan,
                    'jumlah_bayar' => $request->nominal,
                    'sisa_tagihan' => $request->status_tagihan == 'Lunas' ? 0 : $request->nominal,
                    'status_tagihan' => $request->status_tagihan,
                    'cicilan_ke' => $request->status_tagihan == 'Dicicil' ? $request->cicilan_ke : null
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan (ADMIN)
     */
    public function destroy($id_tagihan)
    {
        $tagihan = Tagihan::findOrFail($id_tagihan);

        // HAPUS DATA DENGAN TRANSACTION
        DB::transaction(function () use ($tagihan) {
            DetailTagihan::where('id_tagihan', $tagihan->id_tagihan)->delete();
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
            // Menggunakan Null Coalescing Operator untuk memperingkas kode
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

        // Fallback jika id siswa masih kosong
        $siswaId = $siswaId ?? Siswa::value('id'); 

        $tagihans = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', '!=', 'Lunas')
            ->with('tagihan')
            ->get();

        $tagihanLunas = DetailTagihan::where('id_siswa', $siswaId)
            ->where('status_tagihan', 'Lunas')
            ->with('tagihan')
            ->get();

        return view('ortu.tagihan', compact('tagihans', 'tagihanLunas'));
    }
}