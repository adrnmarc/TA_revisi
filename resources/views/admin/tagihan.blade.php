@extends('layouts.admin')

@section('title', 'Kelola Tagihan')
@section('page_title', 'Kelola Tagihan')

@section('content')
    {{-- STYLE TAMBAHAN UNTUK MENGHAPUS SPINNER --}}
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold flex items-center gap-2 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    {{-- KOTAK NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="mb-4 p-4 text-sm text-rose-800 bg-rose-50 rounded-xl border border-rose-100 font-semibold flex items-center gap-2 shadow-sm animate-shake">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- KOTAK NOTIFIKASI VALIDASI GAGAL --}}
    @if($errors->any())
        <div class="mb-4 p-4 text-sm text-rose-800 bg-rose-50 rounded-xl border border-rose-100 font-semibold shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                <span>Gagal Menyimpan Data! Periksa kembali inputan Anda:</span>
            </div>
            <ul class="list-disc pl-6 font-medium text-xs space-y-1 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                            <td class="py-4 px-6 text-slate-900 font-semibold kolom-siswa">{{ optional($tagihan->siswa)->nama ?? 'Tidak Diketahui' }}</td>
                            <td class="py-4 px-6 kolom-jenis">
                                {{ $tagihan->nama_tagihan }}
                                @if(optional($tagihan->detailTagihan)->status_tagihan == 'Dicicil' && optional($tagihan->detailTagihan)->cicilan_ke)
                                    <span class="text-xs text-amber-600 font-semibold block">(Cicilan Ke-{{ $tagihan->detailTagihan->cicilan_ke }})</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 font-normal">
                                {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo ?? $tagihan->created_at)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-slate-900 font-semibold">
                                Rp {{ number_format(optional($tagihan->detailTagihan)->jumlah_bayar ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $status = optional($tagihan->detailTagihan)->status_tagihan ?? 'Belum Lunas';
                                @endphp

                                @if($status == 'Lunas')
                                    <span class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-100">
                                        Lunas
                                    </span>
                                @elseif($status == 'Dicicil')
                                    <span class="px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 rounded-lg border border-amber-100">
                                        Dicicil
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg border border-rose-100">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                            class="btn-edit-tagihan p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer"
                                            data-id="{{ $tagihan->id_tagihan }}"
                                            data-nis="{{ $tagihan->nis }}"
                                            data-kategori-id="{{ $tagihan->kategori_tagihan_id }}"
                                            data-nominal="{{ optional($tagihan->detailTagihan)->jumlah_bayar ?? 0 }}"
                                            data-tanggal="{{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->format('Y-m-d') }}"
                                            data-status="{{ optional($tagihan->detailTagihan)->status_tagihan ?? 'Belum Lunas' }}"
                                            data-cicilan="{{ optional($tagihan->detailTagihan)->cicilan_ke ?? '' }}">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <form action="/admin/tagihan/{{ $tagihan->id_tagihan }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data tagihan ini?')">
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
                </tbody>
            </table>
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
                            <option value="{{ $siswa->nis }}">{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Tagihan (Kategori)</label>
                    <select name="kategori_tagihan_id" id="buat_kategori_tagihan" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        <option value="" disabled selected data-harga="">-- Pilih Kategori Tagihan --</option>
                        @foreach($daftarKategori as $kategori)
                            <option value="{{ $kategori->id }}" data-harga="{{ $kategori->harga_default }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nominal (Rupiah)</label>
                    <input type="number" name="nominal" id="buat_nominal" required placeholder="Contoh: 350000" class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
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
                            <option value="{{ $siswa->nis }}">{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Jenis Tagihan (Kategori)</label>
                    <select name="kategori_tagihan_id" id="edit_kategori_tagihan_id" required class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                        @foreach($daftarKategori as $kategori)
                            <option value="{{ $kategori->id }}" data-harga="{{ $kategori->harga_default }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
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
                
                <div id="wrapper_cicilan_edit" class="hidden transition-all duration-300">
                    <label class="text-xs font-semibold text-amber-600 block mb-1">Tahap Cicilan</label>
                    <select name="cicilan_ke" id="edit_cicilan_ke" class="w-full px-4 py-2 text-sm bg-amber-50/50 border border-amber-200 rounded-xl focus:outline-none focus:border-amber-500 focus:bg-white transition-all">
                        <option value="">-- Pilih Cicilan --</option>
                        <option value="1">Cicilan Ke-1</option>
                        <option value="2">Cicilan Ke-2</option>
                        <option value="3">Cicilan Ke-3</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl cursor-pointer transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-md shadow-amber-100 cursor-pointer transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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

            // Live Search
            const inputCari = document.getElementById('inputCari');
            const barisData = document.querySelectorAll('.baris-data');

            inputCari.addEventListener('input', function() {
                const filter = inputCari.value.toLowerCase().trim();
                barisData.forEach(row => {
                    const namaSiswa = row.querySelector('.kolom-siswa').textContent.toLowerCase();
                    const jenisTagihan = row.querySelector('.kolom-jenis').textContent.toLowerCase();
                    if (namaSiswa.includes(filter) || jenisTagihan.includes(filter)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });

            // Otomatisasi Nominal Buat
            const selectKategoriBuat = document.getElementById('buat_kategori_tagihan');
            const inputNominalBuat = document.getElementById('buat_nominal');

            selectKategoriBuat.addEventListener('change', function() {
                inputNominalBuat.value = this.options[this.selectedIndex].getAttribute('data-harga');
            });

            // Otomatisasi Nominal Edit
            const selectKategoriEdit = document.getElementById('edit_kategori_tagihan_id');
            const inputNominalEdit = document.getElementById('edit_nominal');

            selectKategoriEdit.addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                if(harga !== null) inputNominalEdit.value = harga;
            });

            // KUNCI KEYBOARD: ANTI KETIK HURUF DAN TANDA MINUS (-) ATAU PLUS (+)
            [inputNominalBuat, inputNominalEdit].forEach(input => {
                if(input) {
                    input.addEventListener('keydown', function(e) {
                        if (['e', 'E', '-', '+', ',', '.'].includes(e.key)) {
                            e.preventDefault();
                        }
                    });
                }
            });

            // Deteksi Status
            const selectStatusEdit = document.getElementById('edit_status_tagihan');
            const wrapperCicilanEdit = document.getElementById('wrapper_cicilan_edit');
            const selectCicilanKe = document.getElementById('edit_cicilan_ke');

            selectStatusEdit.addEventListener('change', function() {
                if (this.value === 'Dicicil') {
                    wrapperCicilanEdit.classList.remove('hidden');
                    selectCicilanKe.setAttribute('required', 'required');
                } else {
                    wrapperCicilanEdit.classList.add('hidden');
                    selectCicilanKe.removeAttribute('required');
                    selectCicilanKe.value = '';
                }
            });

            // Mapping Edit Modal
            const tombolEdit = document.querySelectorAll('.btn-edit-tagihan');
            tombolEdit.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('formEditTagihan').action = '/admin/tagihan/' + this.getAttribute('data-id');
                    document.getElementById('edit_siswa_id').value = this.getAttribute('data-nis');
                    document.getElementById('edit_kategori_tagihan_id').value = this.getAttribute('data-kategori-id');
                    document.getElementById('edit_nominal').value = this.getAttribute('data-nominal');
                    document.getElementById('edit_tanggal_tagihan').value = this.getAttribute('data-tanggal');
                    
                    const status = this.getAttribute('data-status');
                    document.getElementById('edit_status_tagihan').value = status;
                    
                    if (status === 'Dicicil') {
                        wrapperCicilanEdit.classList.remove('hidden');
                        selectCicilanKe.setAttribute('required', 'required');
                        selectCicilanKe.value = this.getAttribute('data-cicilan');
                    } else {
                        wrapperCicilanEdit.classList.add('hidden');
                        selectCicilanKe.removeAttribute('required');
                        selectCicilanKe.value = '';
                    }
                    
                    document.getElementById('modalEditTagihan').classList.remove('hidden');
                });
            });
        });

        function closeEditModal() {
            document.getElementById('modalEditTagihan').classList.add('hidden');
        }
    </script>
@endsection