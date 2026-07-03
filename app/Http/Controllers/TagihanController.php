<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\DetailTagihan;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    
    public function index()
    {
        $tagihans = DetailTagihan::with(['siswa', 'tagihan'])->latest()->get();
        $daftarSiswa = Siswa::all(); 
        
        return view('admin.tagihan', compact('tagihans', 'daftarSiswa'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tanggal_tagihan' => 'required|date',
        ]);

        $tagihanGlobal = Tagihan::create([
            'nama_tagihan' => 'Tagihan ' . $request->jenis_tagihan,
            'jatuh_tempo'  => \Carbon\Carbon::parse($request->tanggal_tagihan)->addMonth()->format('Y-m-d'),
        ]);

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

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tanggal_tagihan' => 'required|date',
            'status_tagihan' => 'required|in:Belum Lunas,Lunas'
        ]);

        $detail = DetailTagihan::findOrFail($id);
        
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
            ]);
        }

        return redirect('/admin/tagihan')->with('sukses', 'Data tagihan berhasil diperbarui!');
    }

    
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