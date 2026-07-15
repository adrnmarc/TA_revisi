<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\DetailTagihan;
use Illuminate\Support\Facades\Auth;

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
            // Ubah format tanggal menjadi nama bulan dan tahun (Contoh: Juli 2026)
            $bulanTahun = \Carbon\Carbon::parse($request->tanggal_tagihan)->translatedFormat('F Y');
            $namaTagihan = $namaTagihan . ' - ' . $bulanTahun;
        }

        // ================================================================
        // 2. LOGIKA DETEKSI ERROR SPESIFIK (SUDAH LUNAS / BELUM LUNAS)
        // ================================================================
        $tagihanTerdaftar = DetailTagihan::where('id_siswa', $siswa->id)
                            ->where('nama_iuran', $namaTagihan)
                            ->first();

        if ($tagihanTerdaftar) {
            // Jika tagihannya ternyata sudah Lunas
            if ($tagihanTerdaftar->status_tagihan === 'Lunas') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Gagal! Tagihan "' . $namaTagihan . '" untuk ' . $siswa->nama . ' tidak bisa ditambahkan karena sudah dibayar/lunas.');
            } 
            
            // Jika tagihannya sudah ada tapi statusnya belum lunas
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal! Siswa ' . $siswa->nama . ' sudah memiliki tagihan aktif untuk "' . $namaTagihan . '".');
        }
        // ================================================================

        // 3. Simpan tabel tagihans
        $tagihan = Tagihan::create([
            'nis' => $siswa->nis,
            'nama_tagihan' => $namaTagihan, 
            'jatuh_tempo' => $request->tanggal_tagihan, 
        ]);

        // 4. Simpan detail tagihan
        DetailTagihan::create([
            'id_tagihan' => $tagihan->id_tagihan,
            'id_siswa' => $siswa->id,
            'nama_iuran' => $namaTagihan, 
            'jumlah_bayar' => $request->nominal,
            'sisa_tagihan' => $request->nominal,
            'status_tagihan' => 'Belum Lunas', 
        ]);

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
            // Validasi bersyarat: cicilan_ke wajib diisi jika status_tagihan = Dicicil (Batas maks 3x)
            'cicilan_ke' => 'required_if:status_tagihan,Dicicil|nullable|in:1,2,3',
        ], [
            'cicilan_ke.required_if' => 'Tahap cicilan wajib diisi jika status pembayaran diatur ke Dicicil.',
            'cicilan_ke.in' => 'Batas pengisian cicilan maksimal hanya sampai 3x.',
        ]);

        $tagihan = Tagihan::findOrFail($id_tagihan);
        $siswa = Siswa::where('nis', $request->siswa_id)->firstOrFail();

        $tagihan->update([
            'nis' => $siswa->nis,
            'nama_tagihan' => $request->jenis_tagihan,
            'jatuh_tempo' => $request->tanggal_tagihan,
        ]);

        $detail = DetailTagihan::where(
            'id_tagihan',
            $tagihan->id_tagihan
        )->first();

        if ($detail) {
            $detail->update([
                'id_siswa' => $siswa->id,
                'nama_iuran' => $request->jenis_tagihan,
                'jumlah_bayar' => $request->nominal,
                'sisa_tagihan' => $request->status_tagihan == 'Lunas' ? 0 : $request->nominal,
                'status_tagihan' => $request->status_tagihan,
                // Jika statusnya Dicicil ambil input request, jika bukan set kembali ke NULL
                'cicilan_ke' => $request->status_tagihan == 'Dicicil' ? $request->cicilan_ke : null
            ]);
        }

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

        DetailTagihan::where(
            'id_tagihan',
            $tagihan->id_tagihan
        )->delete();

        $tagihan->delete();

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
            if (session()->has('siswa_id')) {
                $siswaId = session('siswa_id');
            }
            elseif (method_exists($user, 'siswa') && $user->siswa) {
                $siswaId = $user->siswa->id;
            } 
            elseif (isset($user->siswa_id)) {
                $siswaId = $user->siswa_id;
            } 
            else {
                $siswa = Siswa::where('email', $user->email)
                    ->orWhere('id', $user->id)
                    ->first();
                $siswaId = $siswa ? $siswa->id : null;
            }
        }

        if (!$siswaId) {
            $siswaId = Siswa::value('id'); 
        }

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