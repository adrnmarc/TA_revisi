@extends('layouts.admin')

@section('title', 'Kelola Tagihan')
@section('page_title', 'Kelola Tagihan')

@section('content')
    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white p-4 rounded-2xl border border-slate-100 shadow-xs mb-6">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-5 h-5"></i>
            </span>
            <input type="text" 
                   id="inputCari"
                   placeholder="Cari nama siswa atau jenis tagihan..." 
                   class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
        </div>

        <button id="btnBukaTagihan" class="w-full sm:w-auto bg-[#1E88E5] hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 shadow-md shadow-blue-100 transition-all cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Buat Tagihan Baru</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabelTagihan">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Siswa</th>
                        <th class="py-4 px-6">Jenis Tagihan</th>
                        <th class="py-4 px-6">Tanggal Tagihan</th>
                        <th class="py-4 px-6">Nominal</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-100">
                    @forelse($tagihans as $tagihan)
                        <tr class="baris-data hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-slate-900 font-semibold kolom-siswa">{{ $tagihan->siswa->nama ?? 'Tidak Diketahui' }}</td>
                            <td class="py-4 px-6 kolom-jenis">{{ $tagihan->nama_iuran ?? $tagihan->jenis_tagihan }}</td>
                            <td class="py-4 px-6 text-xs text-slate-400 font-normal">
                                {{ \Carbon\Carbon::parse($tagihan->created_at)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-slate-900 font-semibold">
                                Rp {{ number_format($tagihan->jumlah_bayar ?? $tagihan->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                @if(($tagihan->status_tagihan ?? $tagihan->status) == 'Lunas')
                                    <span class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-100">Lunas</span>
                                @elseif(($tagihan->status_tagihan ?? $tagihan->status) == 'Dicicil')
                                    <span class="px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 rounded-lg border border-amber-100">Dicicil</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg border border-rose-100">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Edit Terintegrasi Javascript --}}
                                    <button type="button" 
                                            onclick="openEditModal('{{ $tagihan->id_detail ?? $tagihan->id }}', '{{ $tagihan->id_siswa ?? $tagihan->siswa_id }}', '{{ $tagihan->nama_iuran ?? $tagihan->jenis_tagihan }}', '{{ $tagihan->jumlah_bayar ?? $tagihan->nominal }}', '{{ \Carbon\Carbon::parse($tagihan->created_at)->format('Y-m-d') }}', '{{ $tagihan->status_tagihan ?? $tagihan->status }}')"
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    
                                    {{-- Form Hapus dengan Konfirmasi Jelas --}}
                                    <form action="/admin/tagihan/{{ $tagihan->id_detail ?? $tagihan->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data tagihan ini beserta detailnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="barisKosong">
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="credit-card" class="w-8 h-8 text-slate-300"></i>
                                    <span>Belum ada data tagihan di database.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    {{-- Baris Cadangan jika hasil pencarian kosong --}}
                    <tr id="pencarianKosong" class="hidden">
                        <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="search" class="w-8 h-8 text-slate-300"></i>
                                <span>Data yang Anda cari tidak ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500 bg-slate-50/50">
            <span>Total {{ $tagihans->count() }} Tagihan</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed" disabled>Sebelumnya</button>
                <button class="px-3 py-1.5 rounded-lg bg-[#1E88E5] text-white">1</button>
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50">Selanjutnya</button>
            </div>
        </div>
    </div>

    {{-- MODAL BUAT TAGIHAN BARU --}}
    <div id="modalTagihan" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm">Buat Tagihan Baru</h3>
                <button id="btnTutupTagihan" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="/admin/tagihan" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Pilih Siswa</label>
                    <select name="siswa_id" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="">-- Pilih Anak Didik --</option>
                        @foreach($daftarSiswa as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Tagihan</label>
                    <select name="jenis_tagihan" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="" disabled selected>-- Pilih Jenis Tagihan --</option>
                        <option value="Uang Sekolah / Uang Program">Uang Sekolah (Uang Program)</option>
                        <option value="Uang Ekskul">Uang Ekskul</option>
                        <option value="Uang POMG">Uang POMG</option>
                        <option value="Uang MMP">Uang MMP</option>
                        <option value="Uang SPP / Bulan">Uang SPP / Bulan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nominal (Rupiah)</label>
                    <input type="number" name="nominal" required placeholder="Contoh: 350000" class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Tagihan Dibuat</label>
                    <input type="date" name="tanggal_tagihan" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-4">
                    <button type="button" id="btnBatalTagihan" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl cursor-pointer transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#1E88E5] hover:bg-blue-600 rounded-xl shadow-md shadow-blue-100 cursor-pointer transition-all">Simpan Tagihan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT TAGIHAN --}}
    <div id="modalEditTagihan" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm">Edit Data Tagihan</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="formEditTagihan" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Pilih Siswa</label>
                    <select name="siswa_id" id="edit_siswa_id" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        @foreach($daftarSiswa as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Tagihan</label>
                    <select name="jenis_tagihan" id="edit_jenis_tagihan" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="Uang Sekolah / Uang Program">Uang Sekolah (Uang Program)</option>
                        <option value="Uang Ekskul">Uang Ekskul</option>
                        <option value="Uang POMG">Uang POMG</option>
                        <option value="Uang MMP">Uang MMP</option>
                        <option value="Uang SPP / Bulan">Uang SPP / Bulan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nominal (Rupiah)</label>
                    <input type="number" name="nominal" id="edit_nominal" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Tagihan</label>
                    <input type="date" name="tanggal_tagihan" id="edit_tanggal_tagihan" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Status Pembayaran</label>
                    <select name="status_tagihan" id="edit_status_tagihan" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Dicicil">Dicicil</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl cursor-pointer transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-md shadow-amber-100 cursor-pointer transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS JAVASCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Kontrol Modal Tambah
            const modal = document.getElementById('modalTagihan');
            const btnBuka = document.getElementById('btnBukaTagihan');
            const btnTutup = document.getElementById('btnTutupTagihan');
            const btnBatal = document.getElementById('btnBatalTagihan');

            btnBuka.addEventListener('click', function() {
                modal.classList.remove('hidden');
            });

            function tutupModal() {
                modal.classList.add('hidden');
            }

            btnTutup.addEventListener('click', tutupModal);
            btnBatal.addEventListener('click', tutupModal);

            // Fitur Live Search Sederhana di Sisi Klien
            const inputCari = document.getElementById('inputCari');
            const barisData = document.querySelectorAll('.baris-data');
            const pencarianKosong = document.getElementById('pencarianKosong');

            inputCari.addEventListener('input', function() {
                const filter = inputCari.value.toLowerCase().trim();
                let adaDataCocok = false;

                barisData.forEach(row => {
                    const namaSiswa = row.querySelector('.kolom-siswa').textContent.toLowerCase();
                    const jenisTagihan = row.querySelector('.kolom-jenis').textContent.toLowerCase();

                    if (namaSiswa.includes(filter) || jenisTagihan.includes(filter)) {
                        row.classList.remove('hidden');
                        adaDataCocok = true;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                // Tampilkan pesan kosong jika tidak ada baris yang cocok
                if (!adaDataCocok && filter !== "") {
                    pencarianKosong.classList.remove('hidden');
                } else {
                    pencarianKosong.classList.add('hidden');
                }
            });
        });

        // Kontrol Modal Edit Terpisah
        function openEditModal(id, siswaId, jenis, nominal, tanggal, status) {
            document.getElementById('formEditTagihan').action = '/admin/tagihan/' + id;
            document.getElementById('edit_siswa_id').value = siswaId;
            document.getElementById('edit_jenis_tagihan').value = jenis;
            document.getElementById('edit_nominal').value = nominal;
            document.getElementById('edit_tanggal_tagihan').value = tanggal;
            document.getElementById('edit_status_tagihan').value = status;
            
            document.getElementById('modalEditTagihan').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEditTagihan').classList.add('hidden');
        }
    </script>
@endsection