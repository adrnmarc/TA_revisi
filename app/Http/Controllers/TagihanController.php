<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\DetailTagihan;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar semua detail tagihan siswa (1 baris per iuran)
     */
    public function index()
    {
        $tagihans = DetailTagihan::with(['siswa', 'tagihan'])->latest()->get();
        $daftarSiswa = Siswa::all(); 
        
        return view('admin.tagihan', compact('tagihans', 'daftarSiswa'));
    }

    /**
     * Menyimpan data tagihan baru secara utuh (Tidak dipecah di tabel utama)
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tanggal_tagihan' => 'required|date',
        ]);

        // 1. Ambil data siswa berdasarkan ID
        $siswa = Siswa::findOrFail($request->siswa_id);

        // 2. Buat data induk tagihan global (Tetap 1 record utuh)
        $tagihanGlobal = Tagihan::create([
            'nama_tagihan' => 'Tagihan ' . $request->jenis_tagihan,
            'jatuh_tempo'  => \Carbon\Carbon::parse($request->tanggal_tagihan)->addMonth()->format('Y-m-d'),
            'nis'          => $siswa->nis,
        ]);

        // 3. Simpan tagihan utuh di DetailTagihan (nominal langsung Rp 1.000.000, tidak dibagi 3)
        DetailTagihan::create([
            'id_tagihan' => $tagihanGlobal->id_tagihan,
            'id_siswa' => $request->siswa_id,
            'nama_iuran' => $request->jenis_tagihan,
            'jumlah_bayar' => $request->nominal,
            'status_tagihan' => 'Belum Lunas',
            'created_at' => $request->tanggal_tagihan . ' 00:00:00',
        ]);

        return redirect('/admin/tagihan')->with('sukses', 'Tagihan baru berhasil dibuat!');
    }

    /**
     * Memperbarui data tagihan tertentu
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tanggal_tagihan' => 'required|date',
            'status_tagihan' => 'required|in:Belum Lunas,Dicicil,Lunas'
        ]);

        $detail = DetailTagihan::findOrFail($id);
        $siswa = Siswa::findOrFail($request->siswa_id);

        $detail->update([
            'id_siswa' => $request->siswa_id,
            'nama_iuran' => $request->jenis_tagihan,
            'jumlah_bayar' => $request->nominal,
            'status_tagihan' => $request->status_tagihan,
            'created_at' => $request->tanggal_tagihan . ' 00:00:00',
        ]);

        if ($detail->tagihan) {
            $detail->tagihan->update([
                'nama_tagihan' => 'Tagihan ' . $request->jenis_tagihan, 
                'jatuh_tempo'  => \Carbon\Carbon::parse($request->tanggal_tagihan)->addMonth()->format('Y-m-d'),
                'nis'          => $siswa->nis,
            ]);
        }

        return redirect('/admin/tagihan')->with('sukses', 'Data tagihan berhasil diperbarui!');
    }

    /**
     * Menghapus data tagihan beserta pembayaran terkait
     */
    public function destroy($id)
    {
        $detail = DetailTagihan::findOrFail($id);
        $idTagihanInduk = $detail->id_tagihan;

        Pembayaran::where('id_detail', $detail->id_detail)->delete();
        $detail->delete();

        Tagihan::where('id_tagihan', $idTagihanInduk)->delete();

        return redirect('/admin/tagihan')->with('sukses', 'Tagihan berhasil dihapus dari sistem!');
    }
}