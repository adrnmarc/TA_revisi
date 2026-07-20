@extends('layouts.admin')

@section('title', 'Kelola Siswa')
@section('page_title', 'Kelola Data Siswa')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    .font-display { font-family: 'Space Grotesk', system-ui, sans-serif; }
    .font-body    { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
</style>

<div class="font-body">

    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    {{-- Kotak Peringatan Validasi Laravel (Back-end) --}}
    @if($errors->any())
        <div class="mb-4 p-4 text-sm text-rose-800 bg-rose-50 rounded-xl border border-rose-100 font-semibold">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>Gagal Menyimpan Data! Periksa kembali inputan Anda:</span>
            </div>
            <ul class="list-disc pl-5 font-medium text-xs space-y-1 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bar Pencarian dan Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white p-4 rounded-2xl border border-slate-100 shadow-xs mb-6">

        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-5 h-5"></i>
            </span>
            <input type="text" id="inputCariSiswa" placeholder="Cari nama atau NIS siswa..."
                class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
        </div>

        <button id="btnBukaSiswa" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-emerald-100 transition-all">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Siswa Baru</span>
        </button>

    </div>

    {{-- Tabel Data Siswa --}}
    <div class="relative bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">

        <span class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500"></span>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabelSiswa">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">NIS</th>
                        <th class="py-4 px-6">Nama Siswa</th>
                        <th class="py-4 px-6">Kelas</th>
                        <th class="py-4 px-6">Wali Kelas</th>
                        <th class="py-4 px-6">Nama Orang Tua</th>
                        <th class="py-4 px-6">Kontak</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-100">

                    @forelse($daftarSiswa as $siswa)

                    @php
                        $palet = ['bg-blue-50 text-[#1E88E5]','bg-emerald-50 text-emerald-600','bg-amber-50 text-amber-600','bg-rose-50 text-rose-500','bg-violet-50 text-violet-600'];
                        $warna = $palet[crc32($siswa->nama) % count($palet)];

                        $kelasStyle = match(true) {
                            str_contains($siswa->kelas, 'TK A - Kelompok 1') => 'bg-sky-50 text-sky-700',
                            str_contains($siswa->kelas, 'TK A - Kelompok 2') => 'bg-sky-50 text-sky-700',
                            str_contains($siswa->kelas, 'TK B - Kelompok 1') => 'bg-violet-50 text-violet-700',
                            str_contains($siswa->kelas, 'TK B - Kelompok 2') => 'bg-violet-50 text-violet-700',
                            default                                           => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <tr class="baris-siswa hover:bg-slate-50/50 transition-colors">

                        <td class="py-4 px-6 font-display font-semibold text-slate-900 kolom-nis">{{ $siswa->nis }}</td>

                        <td class="py-4 px-6 kolom-nama">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $warna }} font-display font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($siswa->nama,0,2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-slate-900 font-bold">{{ $siswa->nama }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-6">
                            <span class="px-2 py-1 {{ $kelasStyle }} rounded-md text-xs font-semibold">{{ $siswa->kelas }}</span>
                        </td>

                        <td class="py-4 px-6 text-xs">{{ $siswa->wali }}</td>
                        <td class="py-4 px-6 text-xs">{{ $siswa->nama_orangtua }}</td>

                        <td class="py-4 px-6 text-xs font-mono text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                                {{ $siswa->kontak }}
                            </span>
                        </td>

                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-1">

                                <button type="button"
                                    class="btn-edit-siswa p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                    data-id="{{ $siswa->id }}"
                                    data-nis="{{ $siswa->nis }}"
                                    data-nama="{{ $siswa->nama }}"
                                    data-kelas="{{ $siswa->kelas }}"
                                    data-wali="{{ $siswa->wali }}"
                                    data-nama_orangtua="{{ $siswa->nama_orangtua }}"
                                    data-kontak="{{ $siswa->kontak }}"
                                    data-jenis_kelamin="{{ $siswa->jenis_kelamin }}"
                                    data-tanggal_lahir="{{ $siswa->tanggal_lahir }}"
                                    data-alamat="{{ $siswa->alamat }}">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>

                                <form action="/admin/siswa/{{ $siswa->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr id="barisKosong">
                        <td colspan="7" class="py-14 text-center text-slate-400 font-medium">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="users" class="w-8 h-8 text-slate-300"></i>
                                <span>Belum ada data siswa di database.</span>
                            </div>
                        </td>
                    </tr>

                    @endforelse

                    <tr id="pencarianKosong" class="hidden">
                        <td colspan="7" class="py-14 text-center text-slate-400 font-medium">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="search" class="w-8 h-8 text-slate-300"></i>
                                <span>Siswa yang Anda cari tidak ditemukan.</span>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

    {{-- MODAL TAMBAH SISWA --}}
    <div id="modalSiswa" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-800 text-sm">Tambah Siswa Baru</h3>
                </div>
                <button id="btnTutupSiswa" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="/admin/siswa" method="POST" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nomor Induk Siswa (NIS)</label>
                    <input type="text" name="nis" id="buat_nis" required placeholder="Isi menggunakan angka..."
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                    <div id="error_buat_nis" class="text-xs text-rose-600 font-semibold mt-1 hidden">Input Gagal! NIS harus berupa angka penuh.</div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" required placeholder="Masukkan nama lengkap siswa"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Kelas</label>
                    <select name="kelas" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        <option value="TK A - Kelompok 1">TK A - Kelompok 1</option>
                        <option value="TK A - Kelompok 2">TK A - Kelompok 2</option>
                        <option value="TK B - Kelompok 1">TK B - Kelompok 1</option>
                        <option value="TK B - Kelompok 2">TK B - Kelompok 2</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Wali Kelas</label>
                    <input type="text" name="wali" required placeholder="Nama guru / wali kelas"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_orangtua" required placeholder="Nama ayah / ibu / wali"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Kontak No. HP / WhatsApp</label>
                    <input type="text" name="kontak" required placeholder="Contoh: 08123456789"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center text-sm font-medium text-slate-600">
                            <input type="radio" name="jenis_kelamin" value="Laki-laki" checked class="mr-2 text-emerald-600 focus:ring-emerald-400">
                            Laki-laki
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600">
                            <input type="radio" name="jenis_kelamin" value="Perempuan" class="mr-2 text-emerald-600 focus:ring-emerald-400">
                            Perempuan
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Alamat Rumah</label>
                    <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap rumah siswa..."
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" id="btnBatalSiswa" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-100 transition-all">Simpan Data</button>
                </div>

            </form>
        </div>
    </div>

    {{-- MODAL EDIT SISWA --}}
    <div id="modalEditSiswa" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-800 text-sm">Edit Data Siswa</h3>
                </div>
                <button type="button" onclick="closeEditSiswaModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="formEditSiswa" action="" method="POST" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nomor Induk Siswa (NIS)</label>
                    <input type="text" name="nis" id="edit_nis" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                    <div id="error_edit_nis" class="text-xs text-rose-600 font-semibold mt-1 hidden">Input Gagal! NIS harus berupa angka penuh.</div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Kelas</label>
                    <select name="kelas" id="edit_kelas" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        <option value="TK A - Kelompok 1">TK A - Kelompok 1</option>
                        <option value="TK A - Kelompok 2">TK A - Kelompok 2</option>
                        <option value="TK B - Kelompok 1">TK B - Kelompok 1</option>
                        <option value="TK B - Kelompok 2">TK B - Kelompok 2</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Wali Kelas</label>
                    <input type="text" name="wali" id="edit_wali" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_orangtua" id="edit_nama_orangtua" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Kontak No. HP / WhatsApp</label>
                    <input type="text" name="kontak" id="edit_kontak" required
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center text-sm font-medium text-slate-600">
                            <input type="radio" name="jenis_kelamin" id="edit_jk_l" value="Laki-laki" class="mr-2 text-emerald-600 focus:ring-emerald-400">
                            Laki-laki
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600">
                            <input type="radio" name="jenis_kelamin" id="edit_jk_p" value="Perempuan" class="mr-2 text-emerald-600 focus:ring-emerald-400">
                            Perempuan
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Alamat Rumah</label>
                    <textarea name="alamat" id="edit_alamat" rows="2"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeEditSiswaModal()" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-md shadow-amber-100 transition-all">Simpan Perubahan</button>
                </div>

            </form>
        </div>
    </div>

</div>

{{-- SCRIPTS KONTROL JAVASCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Kontrol Modal Tambah Siswa
        const modalSiswa = document.getElementById('modalSiswa');
        const btnBukaSiswa = document.getElementById('btnBukaSiswa');
        const btnTutupSiswa = document.getElementById('btnTutupSiswa');
        const btnBatalSiswa = document.getElementById('btnBatalSiswa');

        if (btnBukaSiswa) {
            btnBukaSiswa.addEventListener('click', () => modalSiswa.classList.remove('hidden'));
        }
        if (btnTutupSiswa) {
            btnTutupSiswa.addEventListener('click', () => modalSiswa.classList.add('hidden'));
        }
        if (btnBatalSiswa) {
            btnBatalSiswa.addEventListener('click', () => modalSiswa.classList.add('hidden'));
        }

        // Click Listener untuk Ambil Data Attributes Tombol Edit Siswa
        const tombolEdit = document.querySelectorAll('.btn-edit-siswa');
        tombolEdit.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nis = this.getAttribute('data-nis');
                const nama = this.getAttribute('data-nama');
                const kelas = this.getAttribute('data-kelas');
                const wali = this.getAttribute('data-wali');
                const nama_orangtua = this.getAttribute('data-nama_orangtua');
                const kontak = this.getAttribute('data-kontak');
                const jenis_kelamin = this.getAttribute('data-jenis_kelamin');
                const tanggal_lahir = this.getAttribute('data-tanggal_lahir');
                const alamat = this.getAttribute('data-alamat');

                openEditSiswaModal(id, nis, nama, kelas, wali, nama_orangtua, kontak, jenis_kelamin, tanggal_lahir, alamat);
            });
        });

        // Live Search Sisi Klien untuk Tabel Siswa
        const inputCariSiswa = document.getElementById('inputCariSiswa');
        const barisSiswa = document.querySelectorAll('.baris-siswa');
        const pencarianKosong = document.getElementById('pencarianKosong');

        if (inputCariSiswa) {
            inputCariSiswa.addEventListener('input', function () {
                const filter = this.value.toLowerCase().trim();
                let adaCocok = false;

                barisSiswa.forEach(row => {
                    const nis = row.querySelector('.kolom-nis').textContent.toLowerCase();
                    const nama = row.querySelector('.kolom-nama').textContent.toLowerCase();

                    if (nis.includes(filter) || nama.includes(filter)) {
                        row.classList.remove('hidden');
                        adaCocok = true;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (!adaCocok && filter !== "") {
                    pencarianKosong.classList.remove('hidden');
                } else {
                    pencarianKosong.classList.add('hidden');
                }
            });
        }

        // PENGUNCI DAN PEMBERSIH HURUF OTOMATIS (REAL-TIME REGEX)
        const buatNis = document.getElementById('buat_nis');
        const errorBuatNis = document.getElementById('error_buat_nis');
        const editNis = document.getElementById('edit_nis');
        const errorEditNis = document.getElementById('error_edit_nis');

        if (buatNis) {
            buatNis.addEventListener('input', function () {
                if (/[^0-9]/.test(this.value)) {
                    errorBuatNis.classList.remove('hidden');
                    this.value = this.value.replace(/[^0-9]/g, '');
                } else {
                    errorBuatNis.classList.add('hidden');
                }
            });
        }

        if (editNis) {
            editNis.addEventListener('input', function () {
                if (/[^0-9]/.test(this.value)) {
                    errorEditNis.classList.remove('hidden');
                    this.value = this.value.replace(/[^0-9]/g, '');
                } else {
                    errorEditNis.classList.add('hidden');
                }
            });
        }
    });

    // Fungsi Buka Modal Edit Siswa & Tarik Data Lama ke Kolom Input
    function openEditSiswaModal(id, nis, nama, kelas, wali, nama_orangtua, kontak, jenis_kelamin, tanggal_lahir, alamat) {
        document.getElementById('formEditSiswa').action = '/admin/siswa/' + id;
        document.getElementById('edit_nis').value = nis;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_kelas').value = kelas;
        document.getElementById('edit_wali').value = wali;
        document.getElementById('edit_nama_orangtua').value = nama_orangtua;
        document.getElementById('edit_kontak').value = kontak;
        document.getElementById('edit_tanggal_lahir').value = tanggal_lahir || '';
        document.getElementById('edit_alamat').value = alamat || '';

        if (jenis_kelamin === 'Laki-laki') {
            document.getElementById('edit_jk_l').checked = true;
        } else if (jenis_kelamin === 'Perempuan') {
            document.getElementById('edit_jk_p').checked = true;
        }

        document.getElementById('error_edit_nis').classList.add('hidden');
        document.getElementById('modalEditSiswa').classList.remove('hidden');
    }

    function closeEditSiswaModal() {
        document.getElementById('modalEditSiswa').classList.add('hidden');
    }

    if (window.lucide) lucide.createIcons();
</script>

@endsection