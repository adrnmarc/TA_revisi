<?php

namespace App\Http\Controllers;

use App\Models\KategoriTagihan;
use Illuminate\Http\Request;

class KategoriTagihanController extends Controller
{
    // 1. Menampilkan daftar kategori
    public function index()
    {
        $kategoris = KategoriTagihan::all();
        return view('admin.kategori', compact('kategoris'));
    }

    // 2. Menyimpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'harga_default' => 'required|numeric|min:0',
            'maksimal_angsuran' => 'required|integer|min:1',
        ]);

        KategoriTagihan::create([
            'nama_kategori' => $request->nama_kategori,
            'harga_default' => $request->harga_default,
            'bisa_dicicil'  => $request->has('bisa_dicicil') ? 1 : 0,
            'maksimal_angsuran' => $request->maksimal_angsuran,
        ]);

        return redirect()->back()->with('sukses', 'Kategori baru berhasil ditambahkan!');
    }

    // 3. Menghapus kategori
    public function destroy($id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        
        // Opsional: Cek apakah kategori ini sudah dipakai di tabel tagihans agar tidak error
        if ($kategori->tagihans()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak bisa hapus! Kategori ini sudah digunakan dalam tagihan.');
        }

        $kategori->delete();
        return redirect()->back()->with('sukses', 'Kategori berhasil dihapus!');
    }
}