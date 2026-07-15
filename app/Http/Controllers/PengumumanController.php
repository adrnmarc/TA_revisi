<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // Menampilkan halaman pengumuman dengan data asli dari database
    public function index()
    {
        $daftarPengumuman = Pengumuman::latest()->get();
        return view('admin.pengumuman', compact('daftarPengumuman'));
    }

    // Menyimpan pengumuman baru dari pop-up modal
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'isi' => 'required|string',
        ]);

        Pengumuman::create([
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'isi' => $request->isi,
            'kategori' => $request->kategori ?? 'umum'
        ]);

        return redirect('/admin/pengumuman')->with('sukses', 'Pengumuman baru berhasil diterbitkan!');
    }

    // Menghapus pengumuman
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect('/admin/pengumuman')->with('sukses', 'Pengumuman berhasil dihapus!');
    }

    // Fungsi baru untuk halaman depan (landing page)
    public function landingPage()
    {
        // Mengambil data pengumuman terbaru dari database
        $pengumuman = Pengumuman::latest()->get();

        // Mengembalikan view landing page dengan membawa data pengumuman
        return view('layouts.landing', compact('pengumuman'));
    }

}