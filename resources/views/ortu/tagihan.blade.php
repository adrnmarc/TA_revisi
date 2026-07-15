@extends('layouts.ortu')

@section('header', 'Tagihan')

@section('content')
<div class="pt-8 px-8 max-w-7xl mx-auto pb-12">

    {{-- Card Ringkasan Sisa Tagihan --}}
    <div class="bg-blue-600 rounded-3xl p-8 text-white mb-10 shadow-xl flex justify-between items-center">
        <div>
            <p class="text-blue-100 text-sm font-medium">Total Sisa Tagihan Aktif</p>
            <h1 class="text-4xl font-bold mt-1" id="ringkasan-total-sisa">Rp 0</h1>
            <p class="text-xs text-blue-200 mt-2">Segera lakukan pembayaran untuk melunasi sisa tagihan.</p>
        </div>
        <div class="text-white opacity-80">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('sukses') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error / Gagal --}}
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Validasi Input Gagal --}}
    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-rose-800">Mohon periksa kembali form pembayaran Anda:</p>
                    <ul class="list-disc pl-5 mt-1 text-xs text-rose-700 space-y-1 font-semibold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Utama Pembayaran --}}
    <form id="form-pembayaran" action="{{ url('ortu/bayar-banyak') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <h3 class="text-xl font-bold text-gray-800 mb-6">Tagihan Aktif</h3>

        @if($tagihans->isEmpty())
            <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                <p class="text-gray-500">Tidak ada tagihan aktif yang perlu dibayar.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @foreach ($tagihans as $tagihan)
                    <div class="bg-white rounded-2xl border-2 border-gray-100 p-6 shadow-sm hover:shadow-md transition duration-300 relative">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg leading-tight">{{ $tagihan->nama_iuran }}</h4>
                                <span class="text-xs font-semibold text-red-500 mt-1 block">
                                    Jatuh Tempo: {{ $tagihan->tagihan ? \Carbon\Carbon::parse($tagihan->tagihan->jatuh_tempo)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>
                            @if($tagihan->status_tagihan == 'Belum Lunas')
                                <span class="bg-red-50 text-red-600 text-xs px-2.5 py-1 rounded-full font-bold">BELUM LUNAS</span>
                            @elseif($tagihan->status_tagihan == 'Menyicil' || $tagihan->status_tagihan == 'Mencicil')
                                <span class="bg-amber-50 text-amber-600 text-xs px-2.5 py-1 rounded-full font-bold">MENCICIL</span>
                            @else
                                <span class="bg-blue-50 text-blue-600 text-xs px-2.5 py-1 rounded-full font-bold">{{ $tagihan->status_tagihan }}</span>
                            @endif
                        </div>

                        <div class="border-t border-gray-50 pt-4 mt-4">
                            <p class="text-xs text-gray-400">Sisa Tagihan</p>
                            <h5 class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</h5>
                            @if(isset($tagihan->pembayarans_sum_jumlah_diterima) && $tagihan->pembayarans_sum_jumlah_diterima > 0)
                                <p class="text-xs text-emerald-600 mt-1 font-medium">Sudah dibayar: Rp {{ number_format($tagihan->pembayarans_sum_jumlah_diterima, 0, ',', '.') }}</p>
                            @endif
                        </div>

                        {{-- Panel Pilihan Pembayaran di Setiap Card --}}
                        <div class="bg-blue-50/50 rounded-xl p-4 mt-6 border border-blue-100/50">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="tagihan_id[]" value="{{ $tagihan->id_detail }}" 
                                       class="tagihan-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5" 
                                       data-sisa="{{ $tagihan->sisa_tagihan }}">
                                <span class="text-sm font-bold text-blue-800 select-none">Pilih Tagihan Ini</span>
                            </label>

                            @if(\Illuminate\Support\Str::contains(strtolower($tagihan->nama_iuran), 'spp'))
                                <div class="mt-3">
                                    <span class="text-xs font-bold text-blue-900 block mb-1">BAYAR UNTUK BEBERAPA BULAN?</span>
                                    <select name="jumlah_bulan[{{ $tagihan->id_detail }}]" class="bulan-select block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 py-2">
                                        <option value="1">1 Bulan (Bulan ini saja)</option>
                                        <option value="2">2 Bulan</option>
                                        <option value="3">3 Bulan</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12">1 Tahun Lunas</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="jumlah_bulan[{{ $tagihan->id_detail }}]" class="bulan-select" value="1">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-8">
                <button type="button" onclick="bukaModalKonfirmasi()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition duration-200 inline-flex items-center space-x-2">
                    <span>Lanjutkan Pembayaran</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- MODAL KONFIRMASI PEMBAYARAN --}}
        <div id="modal-pembayaran" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl p-8 relative transform scale-95 transition-all duration-300">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Pembayaran</h3>
                    <button type="button" onclick="tutupModalKonfirmasi()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Panduan Pembayaran dan Total Otomatis --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6">
                    <div class="flex items-start space-x-3 mb-4">
                        <div class="bg-blue-100 text-blue-600 rounded-lg p-2 mt-0.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-800">Panduan Transfer</p>
                            <p class="text-xs text-blue-600">Silakan lakukan transfer ke rekening berikut:</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-xl border border-blue-100 mb-4 shadow-sm">
                        <span class="text-xs font-bold text-gray-400 tracking-wider">BANK BCA</span>
                        <h4 class="text-2xl font-black text-blue-700 tracking-wide mt-1">123 4567 890</h4>
                        <span class="text-xs font-semibold text-gray-600 block mt-1">a.n. TK Mutiara Bogor</span>
                    </div>

                    {{-- TOTAL HARUS DIBAYAR --}}
                    <div class="border-t border-blue-100 pt-4 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-700">Total Harus Ditransfer:</span>
                        <span id="total-transfer-modal" class="text-2xl font-black text-emerald-600">Rp 0</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                    <input type="file" name="bukti_bayar" required class="block w-full border border-gray-200 rounded-xl text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 file:bg-gray-50 file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold file:px-4 file:py-3 file:mr-4 file:cursor-pointer">
                    <p class="text-[10px] text-gray-400 mt-2">* Format gambar wajib JPG, JPEG, atau PNG. Maksimal ukuran file 5 MB.</p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="tutupModalKonfirmasi()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition duration-200">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200">
                        Kirim Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.tagihan-checkbox');
    const modalTotalText = document.getElementById('total-transfer-modal');
    const ringkasanSisaText = document.getElementById('ringkasan-total-sisa');

    // Helper format mata uang Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(angka);
    }

    // Hitung total sisa tagihan untuk bagian Ringkasan atas (Card Biru)
    function hitungRingkasanTotal() {
        let totalSisaSemua = 0;
        checkboxes.forEach(checkbox => {
            totalSisaSemua += parseFloat(checkbox.getAttribute('data-sisa')) || 0;
        });
        if (ringkasanSisaText) {
            ringkasanSisaText.textContent = formatRupiah(totalSisaSemua);
        }
    }

    // Hitung total bayar berdasarkan tagihan yang dicentang dan dropdown bulan
    window.hitungTotalBayar = function() {
        let totalSemua = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const idDetail = checkbox.value;
                const sisaSatuBulan = parseFloat(checkbox.getAttribute('data-sisa')) || 0;
                
                // Cari dropdown bulan milik tagihan ini
                const selectBulan = document.querySelector(`select[name="jumlah_bulan[${idDetail}]"]`) || 
                                    document.querySelector(`input[name="jumlah_bulan[${idDetail}]"]`);
                
                const jumlahBulan = selectBulan ? parseInt(selectBulan.value) : 1;
                
                totalSemua += (sisaSatuBulan * jumlahBulan);
            }
        });

        if (modalTotalText) {
            modalTotalText.textContent = formatRupiah(totalSemua);
        }
        return totalSemua;
    }

    // Event listener saat checkbox diubah atau dropdown diubah
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', window.hitungTotalBayar);
    });

    document.querySelectorAll('.bulan-select').forEach(select => {
        select.addEventListener('change', window.hitungTotalBayar);
    });

    // Jalankan kalkulasi total sisa di card biru saat pertama kali reload halaman
    hitungRingkasanTotal();
});

// Fungsi untuk mengontrol Modal
function bukaModalKonfirmasi() {
    const checkedBoxes = document.querySelectorAll('.tagihan-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Silakan pilih minimal satu tagihan yang ingin dibayar!');
        return;
    }

    // Hitung ulang nominal saat modal akan dibuka
    window.hitungTotalBayar();

    const modal = document.getElementById('modal-pembayaran');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupModalKonfirmasi() {
    const modal = document.getElementById('modal-pembayaran');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}
</script>
@endsection