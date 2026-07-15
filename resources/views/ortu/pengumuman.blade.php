@extends('layouts.ortu')
@section('header', 'Pengumuman')

@section('content')
<div class="pt-8 px-8 max-w-5xl mx-auto pb-12">
    
    {{-- Container utama agar judul dan daftar menyatu --}}
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
        
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
            <h2 class="text-2xl font-black text-slate-800">Pusat Informasi</h2>
            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full uppercase">
                {{ $pengumuman->count() }} Pengumuman
            </span>
        </div>

        <div class="space-y-4">
            @forelse($pengumuman as $item)
                {{-- Kartu yang sekarang berada di dalam bingkai putih --}}
                <div onclick="bukaDetail('{{ addslashes($item->judul) }}', '{{ addslashes($item->isi) }}', '{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}')" 
                     class="group bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:bg-white hover:shadow-lg transition-all duration-300 flex gap-6 items-start cursor-pointer">
                    
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-emerald-600 border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        </div>
                    </div>
                    
                    <div class="flex-grow">
                        <h4 class="text-base font-bold text-slate-800 group-hover:text-emerald-700 mb-1">{{ $item->judul }}</h4>
                        <p class="text-slate-500 text-sm line-clamp-2">{{ $item->isi }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-slate-400">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modalDetail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl relative">
        <button onclick="tutupDetail()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 text-xl font-bold p-2">&times;</button>
        <span id="modalTanggal" class="text-[10px] font-bold text-emerald-600 uppercase"></span>
        <h3 id="modalJudul" class="text-2xl font-black text-slate-800 mt-2 mb-4"></h3>
        <p id="modalIsi" class="text-slate-600 leading-relaxed"></p>
        <button onclick="tutupDetail()" class="mt-8 w-full bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition">Tutup</button>
    </div>
</div>

<script>
    function bukaDetail(judul, isi, tanggal) {
        document.getElementById('modalJudul').innerText = judul;
        document.getElementById('modalIsi').innerText = isi;
        document.getElementById('modalTanggal').innerText = tanggal;
        document.getElementById('modalDetail').classList.remove('hidden');
    }
    function tutupDetail() {
        document.getElementById('modalDetail').classList.add('hidden');
    }
</script>
@endsection