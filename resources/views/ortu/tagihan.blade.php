@extends('layouts.ortu')
@section('header', 'Tagihan')

@section('content')
<div class="pt-8 px-8 max-w-7xl mx-auto pb-12">
    
    {{-- Card Ringkasan --}}
    <div class="bg-blue-600 rounded-3xl p-8 text-white mb-10 shadow-xl flex justify-between items-center">
        <div>
            <p class="text-blue-100 text-sm font-medium">Total Sisa Tagihan Aktif</p>
            <h1 class="text-4xl font-bold mt-1">
                Rp {{ number_format($tagihans->sum('sisa_tagihan'), 0, ',', '.') }}
            </h1>
            <p class="text-blue-100 text-xs mt-2">Segera lakukan pembayaran untuk melunasi sisa tagihan.</p>
        </div>
        <div class="bg-blue-500 p-4 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
    </div>

    @if(session('sukses'))
        <div class="mb-6 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold">
            {{ session('sukses') }}
        </div>
    @endif
    @if(session('gagal'))
        <div class="mb-6 p-4 text-sm text-rose-800 bg-rose-50 rounded-xl border border-rose-100 font-semibold">
            {{ session('gagal') }}
        </div>
    @endif

    @php
        $tagihanAktif = $tagihans->whereNotIn('status_tagihan', ['Lunas']);
        $tagihanLunas = $tagihans->where('status_tagihan', 'Lunas');
    @endphp

    <h2 class="text-xl font-bold text-slate-800 mb-4">Tagihan Aktif</h2>
    
    @if($tagihanAktif->count() > 0)
        <form action="{{ url('ortu/bayar-banyak') }}" method="POST" enctype="multipart/form-data" id="formPembayaran">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($tagihanAktif as $tagihan)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-bold text-slate-800">{{ $tagihan->nama_iuran }}</h3>
                                @if($tagihan->status_tagihan == 'Menunggu Verifikasi')
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-amber-100 text-amber-700 uppercase">Menunggu</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-rose-100 text-rose-700 uppercase">
                                        {{ $tagihan->total_dibayar > 0 ? 'Mencicil' : 'Belum Bayar' }}
                                    </span>
                                @endif
                            </div>

                            {{-- LOGIKA BARU UNTUK CICILAN --}}
                            <p class="text-[10px] font-semibold mb-2 {{ str_contains(strtolower($tagihan->nama_iuran), 'program') ? 'text-blue-600' : 'text-rose-500' }}">
                                @if(str_contains(strtolower($tagihan->nama_iuran), 'program'))
                                    Keterangan: Cicilan (Maks 3x)
                                @else
                                    Jatuh Tempo: {{ $tagihan->tagihan ? \Carbon\Carbon::parse($tagihan->tagihan->jatuh_tempo)->format('d M Y') : '-' }}
                                @endif
                            </p>

                            @if(str_contains(strtolower($tagihan->nama_iuran), 'program'))
                                @php
                                    $jmlCicilan = $tagihan->pembayarans->where('status', 'Diterima')->count();
                                @endphp
                                <div class="mb-4 text-[10px] font-bold {{ $jmlCicilan >= 3 ? 'text-rose-600' : 'text-blue-600' }}">
                                    Cicilan: {{ $jmlCicilan }} dari 3 kali
                                </div>
                            @endif
                            
                            <p class="text-xs text-slate-400 mb-1">
                                {{ $tagihan->total_dibayar > 0 ? 'Sisa Tagihan' : 'Total Tagihan' }}
                            </p>
                            <p class="text-lg font-bold text-slate-900 mb-2">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </p>
                            
                            @if($tagihan->total_dibayar > 0)
                                <p class="text-[10px] text-emerald-600 mb-4 italic">
                                    Sudah dibayar: Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>

                        @if($tagihan->status_tagihan != 'Menunggu Verifikasi')
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <label class="flex items-center justify-center gap-2 w-full text-center text-blue-700 text-sm font-semibold cursor-pointer">
                                    <input type="checkbox" name="tagihan_id[]" value="{{ $tagihan->id_detail }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 checkbox-tagihan">
                                    Pilih Tagihan Ini
                                </label>

                                @if(str_contains(strtolower($tagihan->nama_iuran), 'spp'))
                                    <div class="mt-3 pt-3 border-t border-blue-200">
                                        <label class="text-[10px] text-blue-800 font-bold uppercase mb-1 block">Bayar Untuk Berapa Bulan?</label>
                                        <select name="jumlah_bulan[{{ $tagihan->id_detail }}]" class="w-full text-sm border-blue-300 rounded-lg text-slate-700 focus:ring-blue-500">
                                            <option value="1">1 Bulan (Bulan ini saja)</option>
                                            <option value="2">2 Bulan</option>
                                            <option value="3">3 Bulan</option>
                                            <option value="6">6 Bulan</option>
                                            <option value="12">1 Tahun Lunas</option>
                                        </select>
                                        <p class="text-[9px] text-blue-600 mt-1 italic">*Sistem otomatis membuat tagihan bulan berikutnya.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <button disabled class="mt-4 block w-full text-center bg-slate-100 text-slate-400 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed border border-transparent">
                                Sedang Diproses
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($tagihanAktif->where('status_tagihan', '!=', 'Menunggu Verifikasi')->count() > 0)
                <div class="flex justify-end mb-12">
                    <button type="button" onclick="bukaModalBayar()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2">
                        <span>Lanjutkan Pembayaran</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            @endif

            {{-- MODAL POPUP (Sama seperti sebelumnya) --}}
            <div id="modalBayar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
                <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl transform scale-100 transition-transform">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-slate-800 text-lg">Konfirmasi Pembayaran</h3>
                        <button type="button" onclick="tutupModalBayar()" class="text-slate-400 hover:text-rose-500 transition-colors bg-slate-100 hover:bg-rose-50 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border-l-4 border-blue-600 p-5 rounded-r-2xl mb-6">
                            <div class="flex gap-3 items-start">
                                <svg class="w-6 h-6 text-blue-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h4 class="font-bold text-blue-900 text-sm mb-1">Panduan Transfer</h4>
                                    <p class="text-xs text-blue-700 mb-3">Transfer sejumlah total tagihan yang Anda pilih ke:</p>
                                    <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-sm inline-block w-full">
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bank BCA</p>
                                        <p class="text-2xl font-black text-blue-700 tracking-wider mb-1">123 4567 890</p>
                                        <p class="text-xs font-semibold text-slate-600">a.n. TK Mutiara Bogor</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Transfer <span class="text-rose-500">*</span></label>
                            <input type="file" name="bukti_bayar" id="inputBukti" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl cursor-pointer">
                            <p class="text-[10px] text-slate-500 mt-2">*Format gambar (JPG, PNG). Maksimal 5MB.</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="tutupModalBayar()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition text-sm">Batal</button>
                        <button type="button" onclick="submitForm()" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition text-sm shadow-md">Kirim Pembayaran</button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="p-8 text-center text-slate-500 bg-white rounded-2xl border border-slate-100 mb-12 shadow-sm">
            Hore! Tidak ada tagihan aktif saat ini.
        </div>
    @endif

    {{-- Riwayat Lunas (Sama seperti sebelumnya) --}}
    <h2 class="text-xl font-bold text-slate-800 mb-4 pt-4 border-t border-slate-200">Riwayat Tagihan Lunas</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tagihanLunas as $tagihan)
            <div class="bg-slate-50 p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between opacity-75">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-slate-600">{{ $tagihan->nama_iuran }}</h3>
                        <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-emerald-100 text-emerald-700 uppercase">Lunas</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold mb-2">
                        Jatuh Tempo: {{ \Carbon\Carbon::parse($tagihan->tagihan->jatuh_tempo)->format('d M Y') }}
                    </p>
                    <p class="text-xs text-slate-400 mb-1">Total Dibayar</p>
                    <p class="text-lg font-bold text-slate-600 mb-2">Rp {{ number_format($tagihan->jumlah_bayar, 0, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-100">Belum ada tagihan yang lunas.</div>
        @endforelse
    </div>
</div>

<script>
    function bukaModalBayar() {
        const checkboxes = document.querySelectorAll('.checkbox-tagihan:checked');
        if (checkboxes.length === 0) { alert('Mohon pilih tagihan dulu!'); return; }
        document.getElementById('modalBayar').classList.remove('hidden');
    }
    function tutupModalBayar() { document.getElementById('modalBayar').classList.add('hidden'); }
    function submitForm() {
        const inputFile = document.getElementById('inputBukti');
        if (inputFile.files.length === 0) { alert('Upload bukti transfer dulu!'); return; }
        document.getElementById('formPembayaran').submit();
    }
</script>
@endsection