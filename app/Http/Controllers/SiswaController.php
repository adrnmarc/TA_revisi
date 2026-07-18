<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $daftarSiswa = Siswa::latest()->get();
        return view('admin.siswa', compact('daftarSiswa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan (Mengunci NIS wajib angka dan menambahkan nama_panggilan)
        $request->validate([
            'nis' => 'required|numeric|unique:siswas,nis', // Menggunakan numeric agar menolak huruf
            'nama' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:255',
            'kelas' => 'required|string',
            'wali' => 'required|string|max:255',
            'kontak' => 'required|string|max:20',
            'nama_orangtua' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
        ], [
            // Kustomisasi Pesan Error dalam Bahasa Indonesia
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi!',
            'nis.numeric' => 'Input Gagal! NIS harus berupa angka penuh, tidak boleh mengandung huruf.',
            'nis.unique' => 'Nomor NIS ini sudah terdaftar di data siswa TK Mutiara Bogor.',
            'nama_panggilan.required' => 'Nama panggilan wajib diisi!',
        ]);

        // 2. Simpan data baru
        Siswa::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'nama_panggilan' => $request->nama_panggilan,
            'kelas' => $request->kelas,
            'wali' => $request->wali,
            'kontak' => $request->kontak,
            'nama_orangtua' => $request->nama_orangtua,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'password' => bcrypt($request->nis),
        ]);

        return redirect('/admin/siswa')->with('sukses', 'Data siswa berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        // 1. Validasi update (Mengunci NIS wajib angka dan menambahkan nama_panggilan)
        $request->validate([
            'nis' => 'required|numeric|unique:siswas,nis,' . $id, // Menggunakan numeric agar menolak huruf
            'nama' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:255',
            'kelas' => 'required|string',
            'wali' => 'required|string|max:255',
            'kontak' => 'required|string|max:20',
            'nama_orangtua' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
        ], [
            // Kustomisasi Pesan Error dalam Bahasa Indonesia
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi!',
            'nis.numeric' => 'Input Gagal! NIS harus berupa angka penuh, tidak boleh mengandung huruf.',
            'nis.unique' => 'Nomor NIS ini sudah digunakan oleh siswa lain.',
            'nama_panggilan.required' => 'Nama panggilan wajib diisi!',
        ]);

        // 2. Update data
        $siswa->update([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'nama_panggilan' => $request->nama_panggilan,
            'kelas' => $request->kelas,
            'wali' => $request->wali,
            'kontak' => $request->kontak,
            'nama_orangtua' => $request->nama_orangtua,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ]);

        return redirect('/admin/siswa')->with('sukses', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect('/admin/siswa')->with('sukses', 'Data siswa berhasil dihapus!');
    }
}