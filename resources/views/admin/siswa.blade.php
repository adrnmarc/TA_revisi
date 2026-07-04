@extends('layouts.admin')

@section('title', 'Kelola Data Siswa')
@section('page_title', 'Kelola Data Siswa')

@section('content')
    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    {{-- Header: Pencarian & Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white p-4 rounded-2xl border border-slate-100 shadow-xs mb-6">
        <div class="relative w-full sm:w-80">
            <input type="text" id="inputCariSiswa" placeholder="Cari NIS, nama, atau kelas..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5]">
        </div>
        <button id="btnTambahSiswa" class="bg-[#1E88E5] hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2">
            <span>Tambah Siswa Baru</span>
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <table class="w-full text-left" id="tabelSiswa">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase">
                <tr>
                    <th class="py-4 px-6">NIS</th>
                    <th class="py-4 px-6">Nama</th>
                    <th class="py-4 px-6">Kelas</th>
                    <th class="py-4 px-6">Wali</th>
                    <th class="py-4 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-600 divide-y divide-slate-100">
                @forelse($daftarSiswa as $siswa)
                    <tr class="baris-siswa hover:bg-slate-50">
                        <td class="py-4 px-6 font-semibold">{{ $siswa->nis }}</td>
                        <td class="py-4 px-6">{{ $siswa->nama }}</td>
                        <td class="py-4 px-6">{{ $siswa->kelas }}</td>
                        <td class="py-4 px-6">{{ $siswa->wali }}</td>
                        <td class="py-4 px-6 flex justify-center gap-3">
                            <button onclick="editSiswa('{{ $siswa->id }}', '{{ $siswa->nis }}', '{{ $siswa->nama }}', '{{ $siswa->kelas }}', '{{ $siswa->wali }}', '{{ $siswa->kontak }}', '{{ $siswa->nama_orangtua }}', '{{ $siswa->jenis_kelamin }}', '{{ $siswa->tanggal_lahir }}', '{{ $siswa->alamat }}')" 
                                    class="text-blue-500 font-bold">Edit</button>
                            <form action="/admin/siswa/{{ $siswa->id }}" method="POST" onsubmit="return confirm('Hapus data {{ $siswa->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada data siswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="fixed inset-0 bg-slate-900/50 hidden z-50 p-4 flex items-start justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 my-10 shadow-xl relative">
            {{-- Tombol Close X Silang --}}
            <button type="button" onclick="tutupModal('modalTambah')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="font-bold text-slate-800 mb-4 text-lg">Tambah Siswa Baru</h3>
            <form action="/admin/siswa" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nis" placeholder="NIS" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                
                <select name="kelas" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                    <option value="" disabled selected>Pilih Kelas / Kelompok</option>
                    <option value="TK A - Kelompok A">TK A - Kelompok A</option>
                    <option value="TK A - Kelompok B">TK A - Kelompok B</option>
                    <option value="TK B - Kelompok A">TK B - Kelompok A</option>
                    <option value="TK B - Kelompok B">TK B - Kelompok B</option>
                </select>
                
                <input type="text" name="wali" placeholder="Wali" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="kontak" placeholder="Kontak" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="nama_orangtua" placeholder="Nama Orang Tua" required class="w-full p-2.5 border rounded-xl bg-slate-50">
                <select name="jenis_kelamin" class="w-full p-2.5 border rounded-xl bg-slate-50">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <input type="date" name="tanggal_lahir" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <textarea name="alamat" placeholder="Alamat" class="w-full p-2.5 border rounded-xl bg-slate-50"></textarea>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="tutupModal('modalTambah')" class="w-1/3 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-blue-600 text-white rounded-xl font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modalEdit" class="fixed inset-0 bg-slate-900/50 hidden z-50 p-4 flex items-start justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 my-10 shadow-xl relative">
            {{-- Tombol Close X Silang --}}
            <button type="button" onclick="tutupModal('modalEdit')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="font-bold text-slate-800 mb-4 text-lg">Edit Data Siswa</h3>
            <form id="formEdit" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input type="text" name="nis" id="edit_nis" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="nama" id="edit_nama" class="w-full p-2.5 border rounded-xl bg-slate-50">
                
                <select name="kelas" id="edit_kelas" class="w-full p-2.5 border rounded-xl bg-slate-50">
                    <option value="TK A - Kelompok A">TK A - Kelompok A</option>
                    <option value="TK A - Kelompok B">TK A - Kelompok B</option>
                    <option value="TK B - Kelompok A">TK B - Kelompok A</option>
                    <option value="TK B - Kelompok B">TK B - Kelompok B</option>
                </select>
                
                <input type="text" name="wali" id="edit_wali" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="kontak" id="edit_kontak" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <input type="text" name="nama_orangtua" id="edit_nama_orangtua" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <select name="jenis_kelamin" id="edit_jenis_kelamin" class="w-full p-2.5 border rounded-xl bg-slate-50">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="w-full p-2.5 border rounded-xl bg-slate-50">
                <textarea name="alamat" id="edit_alamat" class="w-full p-2.5 border rounded-xl bg-slate-50"></textarea>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="tutupModal('modalEdit')" class="w-1/3 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-blue-600 text-white rounded-xl font-semibold">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi Membuka Modal Tambah
        document.getElementById('btnTambahSiswa').onclick = () => {
            document.getElementById('modalTambah').classList.remove('hidden');
        };

        // Fungsi Menutup Modal secara Umum
        function tutupModal(idModal) {
            document.getElementById(idModal).classList.add('hidden');
        }
        
        // Fungsi Membuka dan Mengisi Modal Edit
        function editSiswa(id, nis, nama, kelas, wali, kontak, nama_ortu, jk, tgl, alamat) {
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('formEdit').action = '/admin/siswa/' + id;
            document.getElementById('edit_nis').value = nis;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_wali').value = wali;
            document.getElementById('edit_kontak').value = kontak;
            document.getElementById('edit_kelas').value = kelas;
            document.getElementById('edit_nama_orangtua').value = nama_ortu;
            document.getElementById('edit_jenis_kelamin').value = jk;
            document.getElementById('edit_tanggal_lahir').value = tgl;
            document.getElementById('edit_alamat').value = alamat;
        }

        // Fitur Tambahan: Menutup modal ketika area gelap (backdrop) di luar box diklik
        window.onclick = function(event) {
            const modalTambah = document.getElementById('modalTambah');
            const modalEdit = document.getElementById('modalEdit');
            
            if (event.target === modalTambah) {
                tutupModal('modalTambah');
            }
            if (event.target === modalEdit) {
                tutupModal('modalEdit');
            }
        }
    </script>
@endsection