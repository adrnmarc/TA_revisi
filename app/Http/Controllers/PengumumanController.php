<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // Daftar kategori yang tersedia untuk pengumuman
    public const KATEGORI = [
        'umum'      => 'Umum',
        'pembayaran'=> 'Pembayaran',
        'kegiatan'  => 'Kegiatan',
        'libur'     => 'Libur',
        'penting'   => 'Penting',
    ];

    // Menampilkan halaman pengumuman dengan data asli dari database
    // Catatan: admin sengaja melihat SEMUA pengumuman (termasuk yang terjadwal/belum tayang)
    // supaya bisa dikelola. Yang disembunyikan hanya di sisi publik (landing page & ortu).
    public function index()
    {
        $daftarPengumuman = Pengumuman::orderBy('tanggal', 'desc')->get();
        $kategoriList = self::KATEGORI;

        return view('admin.pengumuman', compact('daftarPengumuman', 'kategoriList'));
    }

    // Menyimpan pengumuman baru dari pop-up modal
    public function store(Request $request)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'tanggal'  => 'required|date',
            'isi'      => 'required|string',
            'kategori' => 'nullable|string|in:' . implode(',', array_keys(self::KATEGORI)),
        ]);

        Pengumuman::create([
            'judul'    => $request->judul,
            'tanggal'  => $request->tanggal,
            'isi'      => $request->isi,
            'kategori' => $request->kategori ?? 'umum',
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
        // Hanya tampilkan pengumuman yang tanggal terbitnya sudah tiba (hari ini atau sebelumnya).
        // Pengumuman dengan tanggal terbit di masa depan otomatis disembunyikan sampai tanggalnya tiba.
        $pengumuman = Pengumuman::whereDate('tanggal', '<=', now())
            ->orderBy('tanggal', 'desc')
            ->get();

        // Mengembalikan view landing page dengan membawa data pengumuman
        return view('layouts.landing', compact('pengumuman'));
    }

    // Fungsi untuk halaman papan mading orang tua
    public function ortuIndex()
    {
        // Sama seperti landing page: pengumuman terjadwal (tanggal terbit di masa depan) belum ditampilkan
        $pengumuman = Pengumuman::whereDate('tanggal', '<=', now())
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('ortu.pengumuman', compact('pengumuman'));
    }
}