@extends('layouts.ortu')

@section('header', 'Tagihan')

@section('content')
<div class="pt-8 px-8 max-w-7xl mx-auto pb-12">

    {{-- Card Ringkasan Sisa Tagihan --}}
    <div class="bg-blue-600 rounded-3xl p-8 text-white mb-10 shadow-xl flex justify-between items-center relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <p class="text-blue-100 text-sm font-medium">Total Sisa Tagihan Aktif</p>
            <h1 class="text-4xl font-bold mt-1" id="ringkasan-total-sisa">Rp 0</h1>
            <p class="text-xs text-blue-200 mt-2">Segera lakukan pembayaran untuk melunasi sisa tagihan.</p>
        </div>
        <div class="text-white opacity-80 relative z-10">
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

    {{-- Notifikasi Error --}}
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

    {{-- Form Utama Pembayaran --}}
    <form id="form-pembayaran" action="{{ url('ortu/bayar-banyak') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <h3 class="text-xl font-bold text-gray-800 mb-6">Tagihan Aktif</h3>

        @if($tagihans->isEmpty())
            <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                <p class="text-gray-500">Tidak ada tagihan aktif yang perlu dibayar.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10 items-start">
                @foreach ($tagihans as $tagihan)
                    @php
                        $namaIuranLower = strtolower($tagihan->nama_iuran);
                        $isSpp = \Illuminate\Support\Str::contains($namaIuranLower, 'spp');
                        $isProgram = \Illuminate\Support\Str::contains($namaIuranLower, 'program') || \Illuminate\Support\Str::contains($namaIuranLower, 'sekolah');

                        $totalTerbayarValid = $tagihan->pembayarans->where('status', '!=', 'Ditolak')->sum('jumlah_diterima');
                        $sisaReal = $tagihan->jumlah_bayar - $totalTerbayarValid;

                        $maxCicilan = 3;
                        $sudahDicicil = isset($tagihan->pembayarans) ? $tagihan->pembayarans->where('status', 'Diterima')->count() : 0;
                        $sisaSlotCicilan = $maxCicilan - $sudahDicicil;
                        $rekomendasiCicilan = ($sisaSlotCicilan > 0) ? ceil($sisaReal / $sisaSlotCicilan) : $sisaReal;

                        // Ada pembayaran (cicilan atau pelunasan) yang masih menunggu dicek admin?
                        $adaPembayaranPending = $tagihan->pembayarans->where('status', 'Menunggu Verifikasi')->isNotEmpty();
                    @endphp

                    <div class="bg-white rounded-2xl border-2 border-gray-100 p-6 shadow-sm hover:shadow-md transition duration-300 relative">
                        <div class="flex justify-between items-start mb-4 gap-4">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-lg leading-tight">{{ $tagihan->nama_iuran }}</h4>
                                <span class="text-xs font-semibold text-red-500 mt-1 block">
                                    Jatuh Tempo: {{ $tagihan->tagihan ? \Carbon\Carbon::parse($tagihan->tagihan->jatuh_tempo)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>

                            @if($tagihan->status_tagihan == 'Belum Lunas')
                                <span class="shrink-0 whitespace-nowrap bg-red-50 text-red-600 text-[10px] px-2.5 py-1.5 rounded-full font-bold border border-red-100 tracking-wide">BELUM LUNAS</span>
                            @elseif(strtolower($tagihan->status_tagihan) == 'ditolak')
                                <span class="shrink-0 whitespace-nowrap bg-rose-50 text-rose-600 text-[10px] px-2.5 py-1.5 rounded-full font-bold border border-rose-200 tracking-wide">DITOLAK</span>
                            @elseif(in_array($tagihan->status_tagihan, ['Menyicil', 'Mencicil', 'Dicicil']))
                                <span class="shrink-0 whitespace-nowrap bg-amber-50 text-amber-600 text-[10px] px-2.5 py-1.5 rounded-full font-bold border border-amber-100 tracking-wide">MENCICIL</span>
                            @else
                                <span class="shrink-0 whitespace-nowrap bg-blue-50 text-blue-600 text-[10px] px-2.5 py-1.5 rounded-full font-bold border border-blue-100 tracking-wide">{{ strtoupper($tagihan->status_tagihan) }}</span>
                            @endif
                        </div>

                        <div class="border-t border-gray-50 pt-4 mt-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Sisa Tagihan</p>
                            <h5 class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($sisaReal, 0, ',', '.') }}</h5>
                        </div>

                        {{-- Bagian Menunggu Verifikasi (Pelunasan penuh) --}}
                        @if($tagihan->status_tagihan == 'Menunggu Verifikasi')
                            <div class="bg-amber-50 rounded-xl p-4 mt-6 border border-amber-200 text-center shadow-inner">
                                <svg class="mx-auto h-8 w-8 text-amber-500 mb-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h6 class="text-sm font-bold text-amber-800">Sedang Diverifikasi</h6>
                                <p class="text-[11px] text-amber-600 mt-1 font-medium">Pembayaran Anda sedang dicek oleh Admin.</p>
                            </div>

                        {{-- Bagian Cicilan Baru Masuk, Menunggu Dicek Admin, Tapi Belum Lunas --}}
                        @elseif($adaPembayaranPending)
                            <div class="bg-amber-50 rounded-xl p-4 mt-6 border border-amber-200 text-center shadow-inner">
                                <svg class="mx-auto h-8 w-8 text-amber-500 mb-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h6 class="text-sm font-bold text-amber-800">Cicilan Sedang Dicek Admin</h6>
                                <p class="text-[11px] text-amber-600 mt-1 font-medium">
                                    Setelah disetujui, sisa tagihan Anda akan berkurang.
                                </p>
                                @if($isProgram && $sisaSlotCicilan > 0)
                                    <p class="text-[11px] text-amber-700 mt-2 font-bold">
                                        Sisa maksimal {{ $sisaSlotCicilan }}x cicilan lagi untuk melunasi.
                                    </p>
                                @endif
                            </div>
                        @else
                            {{-- Status Ditolak --}}
                            @php
                                $pembayaranTerakhir = isset($tagihan->pembayarans) ? $tagihan->pembayarans->last() : null;
                                $isDitolak = $pembayaranTerakhir && strtolower($pembayaranTerakhir->status) === 'ditolak';
                                $alasanTolak = $isDitolak ? $pembayaranTerakhir->keterangan : 'Bukti pembayaran tidak valid atau kurang jelas.';
                            @endphp

                            @if(strtolower($tagihan->status_tagihan) == 'ditolak' || $isDitolak)
                                <div class="bg-rose-50 rounded-xl p-4 mt-5 border border-rose-200 shadow-sm">
                                    <div class="flex items-start space-x-3">
                                        <div class="bg-rose-100 p-1.5 rounded-full mt-0.5">
                                            <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </div>
                                        <div>
                                            <h6 class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Pembayaran Terakhir Ditolak!</h6>
                                            <p class="text-[12px] text-rose-600 mt-1 leading-snug">Alasan Admin: <strong class="text-rose-700 font-bold">"{{ $alasanTolak }}"</strong></p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Info Sisa Cicilan (Khusus Program, jika sudah pernah dicicil) --}}
                            @if($isProgram && $sudahDicicil > 0 && $sisaReal > 0)
                                <div class="bg-blue-50 rounded-xl p-3 mt-4 border border-blue-100 text-center">
                                    <p class="text-[11px] text-blue-700 font-bold">
                                        Sudah dicicil {{ $sudahDicicil }}x dari maksimal {{ $maxCicilan }}x.
                                        Sisa {{ $sisaSlotCicilan }}x cicilan lagi untuk melunasi.
                                    </p>
                                </div>
                            @endif

                            {{-- Form Input --}}
                            <div class="bg-blue-50/50 rounded-xl p-4 mt-4 border border-blue-100/50 relative">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="tagihan_id[]" value="{{ $tagihan->id_detail }}"
                                           class="tagihan-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5 transition"
                                           data-sisa="{{ $sisaReal }}"
                                           data-is-spp="{{ $isSpp ? 'true' : 'false' }}"
                                           data-id="{{ $tagihan->id_detail }}">
                                    <span class="text-sm font-bold text-blue-800 select-none">Pilih Tagihan Ini</span>
                                </label>

                                {{-- Opsi Bulan SPP --}}
                                @if($isSpp)
                                    <div class="mt-3 bg-white p-3 rounded-lg border border-blue-100">
                                        <span class="text-[10px] font-bold text-blue-900 block mb-1.5 uppercase tracking-wider">BAYAR UNTUK BEBERAPA BULAN?</span>
                                        <select name="jumlah_bulan[{{ $tagihan->id_detail }}]" class="bulan-select block w-full rounded-md border-gray-200 text-xs font-semibold text-gray-700 focus:border-blue-500 focus:ring-blue-500 py-2 bg-gray-50 cursor-pointer">
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

                                <div id="wrapper-nominal-{{ $tagihan->id_detail }}" class="mt-4 border-t border-blue-100/50 pt-4 hidden transition-all duration-300">
                                    <label class="block font-bold text-[10px] text-blue-900 mb-2 uppercase tracking-wider">
                                        Nominal yang akan dibayar:
                                    </label>

                                    <input type="number"
                                           id="input_nominal_{{ $tagihan->id_detail }}"
                                           name="nominal_bayar[{{ $tagihan->id_detail }}]"
                                           value="{{ $isProgram ? $rekomendasiCicilan : $sisaReal }}"
                                           max="{{ $sisaReal }}"
                                           min="{{ $isProgram ? '1000' : $sisaReal }}"
                                           {{ !$isProgram ? 'readonly' : '' }}
                                           class="input-nominal-bayar w-full {{ !$isProgram ? 'bg-gray-100 border-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white border-blue-300 text-blue-800 shadow-sm' }} px-3.5 py-2.5 rounded-lg font-bold text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                                           placeholder="Ketik nominal transfer...">

                                    @if($isProgram)
                                        <div class="mt-3">
                                            <p class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded inline-block border border-blue-100">
                                                ℹ️ Maksimal Cicilan: 3 Kali
                                            </p>
                                        </div>
                                    @endif
                                    <div class="flex gap-2 mt-3 flex-wrap">
                                        {{-- Tombol Cicilan (Hanya untuk Program) --}}
                                        @if($isProgram)
                                            <button type="button"
                                                    onclick="aktifkanInput('{{ $tagihan->id_detail }}'); setNominalX('{{ $tagihan->id_detail }}', {{ $rekomendasiCicilan }})"
                                                    class="flex-1 bg-blue-100/70 text-blue-700 text-[10px] px-2 py-2 rounded border border-blue-200 hover:bg-blue-200 transition font-bold uppercase tracking-wider text-center">
                                                 CICILAN
                                            </button>
                                        @endif

                                        <button type="button"
                                                id="btn_lunas_{{ $tagihan->id_detail }}"
                                                onclick="setNominalX('{{ $tagihan->id_detail }}', {{ $sisaReal }})"
                                                class="flex-1 bg-emerald-100 text-emerald-700 text-[10px] px-2 py-2 rounded border border-emerald-200 hover:bg-emerald-200 transition font-bold uppercase tracking-wider text-center">
                                             Lunas (Rp {{ number_format($sisaReal, 0, ',', '.') }})
                                        </button>
                                    </div>

                                    @if(!$isProgram)
                                        <div class="mt-3 flex items-start space-x-1">
                                            <svg class="h-3 w-3 text-amber-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-[9px] text-amber-600 font-semibold leading-tight">Wajib dibayar lunas (tidak dapat dicicil).</p>
                                        </div>
                                    @endif
                                    <p id="error_{{ $tagihan->id_detail }}" class="text-[10px] text-rose-500 mt-2 hidden font-semibold bg-rose-50 p-1.5 rounded text-center">Nominal melampaui sisa tagihan!</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-8 border-t border-gray-200 pt-8">
                <button type="button" onclick="bukaModalKonfirmasi()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg shadow-blue-200 transition duration-200 inline-flex items-center space-x-2 group">
                    <span>Lanjutkan Pembayaran</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- MODAL KONFIRMASI PEMBAYARAN --}}
        <div id="modal-pembayaran" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 relative" id="modal-content">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white text-center relative">
                    <button type="button" onclick="tutupModalKonfirmasi()" class="absolute top-4 right-4 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="mx-auto bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mb-3 backdrop-blur-md border border-white/30 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold tracking-wide">Konfirmasi Pembayaran</h3>
                </div>

                <div class="p-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-5 relative overflow-hidden group hover:border-blue-300 transition">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-full -mr-16 -mt-16 opacity-50 transition-transform group-hover:scale-110"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transfer Ke Rekening</span>
                                <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded font-bold border border-blue-200">BANK BCA</span>
                            </div>
                            <h4 class="text-3xl font-black text-gray-800 tracking-widest mt-2 font-mono">123 4567 890</h4>
                            <span class="text-xs font-semibold text-gray-500 block mt-2 uppercase tracking-wide">A.N. TK Mutiara Bogor</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center bg-emerald-50 border border-emerald-200 p-4 rounded-2xl mb-6 shadow-sm">
                        <div class="flex items-center space-x-2">
                            <div class="bg-emerald-100 p-1.5 rounded-full">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-emerald-900">Total Tagihan</span>
                        </div>
                        <span id="total-transfer-modal" class="text-2xl font-black text-emerald-600 tracking-tight">Rp 0</span>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:bg-blue-50/50 hover:border-blue-400 transition cursor-pointer group">
                            <input type="file" name="bukti_bayar" required accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" id="file-upload">
                            <div class="space-y-2 z-10 relative pointer-events-none">
                                <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-blue-100 transition">
                                    <svg class="h-6 w-6 text-gray-400 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <div class="text-sm text-gray-600" id="file-name-display">
                                    <span class="font-bold text-blue-600">Pilih file</span> atau tarik gambar ke sini
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" onclick="tutupModalKonfirmasi()" class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-4 rounded-xl transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-200 transition duration-200 flex justify-center items-center gap-2 group">
                            <span>Kirim Pembayaran</span>
                        </button>
                    </div>
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
        const fileUpload = document.getElementById('file-upload');
        const fileNameDisplay = document.getElementById('file-name-display');

        // FUNGSI AKTIFKAN INPUT MANUAL
        window.aktifkanInput = function(id) {
            const inputNominal = document.getElementById(`input_nominal_${id}`);
            if (inputNominal) {
                inputNominal.removeAttribute('readonly');
                inputNominal.classList.remove('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
                inputNominal.classList.add('bg-white', 'border-blue-300', 'text-blue-800');
                inputNominal.focus();
            }
        }

        if(fileUpload) {
            fileUpload.addEventListener('change', function(e) {
                if(e.target.files.length > 0) {
                    fileNameDisplay.innerHTML = `<span class="font-bold text-emerald-600 border border-emerald-200 bg-emerald-50 px-2 py-1 rounded truncate block max-w-full">✓ ${e.target.files[0].name}</span>`;
                }
            });
        }

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(angka);
        }

        function hitungRingkasanTotal() {
            let totalSisaSemua = 0;
            checkboxes.forEach(checkbox => {
                totalSisaSemua += parseFloat(checkbox.getAttribute('data-sisa')) || 0;
            });
            if (ringkasanSisaText) {
                ringkasanSisaText.textContent = formatRupiah(totalSisaSemua);
            }
        }

        function handleCheckboxChange(checkbox) {
            const idDetail = checkbox.getAttribute('data-id');
            const wrapper = document.getElementById(`wrapper-nominal-${idDetail}`);
            const inputNominal = document.getElementById(`input_nominal_${idDetail}`);

            if (checkbox.checked) {
                wrapper.classList.remove('hidden');
                setTimeout(() => {
                    wrapper.classList.add('opacity-100');
                    wrapper.classList.remove('opacity-0');
                }, 50);
                inputNominal.removeAttribute('disabled');
            } else {
                wrapper.classList.add('opacity-0');
                wrapper.classList.remove('opacity-100');
                setTimeout(() => {
                    wrapper.classList.add('hidden');
                }, 300);
                inputNominal.setAttribute('disabled', 'true');
            }
            window.hitungTotalBayar();
        }

        window.setNominalX = function(id, nominal) {
            const input = document.getElementById(`input_nominal_${id}`);
            input.value = nominal;
            validateNominalX(id);
            window.hitungTotalBayar();
        }

        function validateNominalX(id) {
        const input = document.getElementById(`input_nominal_${id}`);
        const errorMsg = document.getElementById(`error_${id}`);
        const maxVal = parseFloat(input.getAttribute('max'));

        // Biarkan user mengetik, kita hanya mengecek apakah angkanya valid
        if (input.value !== "" && parseFloat(input.value) > maxVal) {
            input.value = maxVal; // Hanya batasi jika melebihi sisa tagihan
            errorMsg.classList.remove('hidden');
            errorMsg.textContent = "Nominal melebihi sisa!";
        } else if (input.value !== "" && parseFloat(input.value) < 1) {
            // Jangan hapus angka yang diketik, cukup sembunyikan atau beri peringatan
            errorMsg.classList.remove('hidden');
            errorMsg.textContent = "Minimal Rp 1";
        } else {
            errorMsg.classList.add('hidden');
        }

        window.hitungTotalBayar();
    }

        document.querySelectorAll('.bulan-select').forEach(select => {
            select.addEventListener('change', function() {
                const idMatch = this.name.match(/\d+/);
                if (!idMatch) return;
                const id = idMatch[0];
                const checkbox = document.querySelector(`.tagihan-checkbox[data-id="${id}"]`);
                const hargaSatuBulan = parseFloat(checkbox.getAttribute('data-sisa'));
                const jumlahBulan = parseInt(this.value);
                const totalBaru = hargaSatuBulan * jumlahBulan;
                const inputNominal = document.getElementById(`input_nominal_${id}`);
                if (inputNominal) {
                    inputNominal.value = totalBaru;
                    inputNominal.setAttribute('max', totalBaru);
                    inputNominal.setAttribute('min', totalBaru);
                }
                const btnLunas = document.getElementById(`btn_lunas_${id}`);
                if (btnLunas) {
                    btnLunas.innerHTML = `Lunas (${formatRupiah(totalBaru)})`;
                    btnLunas.setAttribute('onclick', `setNominalX('${id}', ${totalBaru})`);
                }
                window.hitungTotalBayar();
            });
        });

        window.hitungTotalBayar = function() {
            let totalSemua = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const idDetail = checkbox.value;
                    const inputNominal = document.getElementById(`input_nominal_${idDetail}`);
                    const nilaiInput = inputNominal ? parseFloat(inputNominal.value) : 0;
                    totalSemua += nilaiInput;
                }
            });
            if (modalTotalText) {
                modalTotalText.textContent = formatRupiah(totalSemua);
            }
            return totalSemua;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this);
            });
        });

        document.querySelectorAll('.input-nominal-bayar').forEach(input => {
            input.addEventListener('input', function() {
                const id = this.getAttribute('id').replace('input_nominal_', '');
                validateNominalX(id);
                window.hitungTotalBayar();
            });
        });

        hitungRingkasanTotal();
    });

    function bukaModalKonfirmasi() {
        const checkedBoxes = document.querySelectorAll('.tagihan-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan pilih minimal satu tagihan yang ingin dibayar!');
            return;
        }

        const total = window.hitungTotalBayar();
        if (total <= 0) {
            alert('Total pembayaran tidak valid atau Rp 0.');
            return;
        }

        const modal = document.getElementById('modal-pembayaran');
        const modalContent = document.getElementById('modal-content');

        modal.classList.remove('hidden');
        void modal.offsetWidth;

        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }

    function tutupModalKonfirmasi() {
        const modal = document.getElementById('modal-pembayaran');
        const modalContent = document.getElementById('modal-content');

        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection