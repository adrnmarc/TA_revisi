<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\DetailTagihan;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan
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
     * Simpan tagihan baru
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

        // 2. Simpan tabel tagihans
        $tagihan = Tagihan::create([
            'nis' => $siswa->nis,
            'nama_tagihan' => $namaTagihan, // Pakai nama yang sudah diolah
            'jatuh_tempo' => $request->tanggal_tagihan, // Ini sudah benar masuk ke DB
        ]);

        // 3. Simpan detail tagihan
        DetailTagihan::create([
            'id_tagihan' => $tagihan->id_tagihan,
            'id_siswa' => $siswa->id,
            'nama_iuran' => $namaTagihan, // Pakai nama yang sudah diolah
            'jumlah_bayar' => $request->nominal,
            'status_tagihan' => 'Belum Bayar', // Ubah ke 'Belum Bayar' agar seragam dengan sistem sebelumnya
        ]);

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan berhasil ditambahkan.');
    }

    /**
     * Update tagihan
     */
    public function update(Request $request, $id_tagihan)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal_tagihan' => 'required|date',
            'status_tagihan' => 'required'
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

                'status_tagihan' => $request->status_tagihan

            ]);

        }

        return redirect()
            ->back()
            ->with('sukses', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan
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
}