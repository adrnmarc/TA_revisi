@extends('layouts.ortu')
@section('header', 'Tagihan')


@section('content')
<div class="pt-8 px-8 max-w-7xl mx-auto">
    
    {{-- Card Ringkasan --}}
    <div class="bg-blue-600 rounded-3xl p-8 text-white mb-10 shadow-xl flex justify-between items-center">
        <div>
            <p class="text-blue-100 text-sm font-medium">Total Tagihan Aktif</p>
            <h1 class="text-4xl font-bold mt-1">
                Rp {{ number_format($tagihans->where('status_tagihan', '!=', 'Lunas')->sum('jumlah_bayar'), 0, ',', '.') }}
            </h1>
            <p class="text-blue-100 text-xs mt-2">Segera lakukan pembayaran sebelum jatuh tempo.</p>
        </div>
        <div class="bg-blue-500 p-4 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
    </div>

    <h2 class="text-xl font-bold text-slate-800 mb-6">Rincian Tagihan</h2>

    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div class="mb-6 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold">
            {{ session('sukses') }}
        </div>
    @endif

    {{-- Grid Kartu Tagihan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tagihans as $tagihan)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-slate-800">{{ $tagihan->nama_iuran }}</h3>
                        @if($tagihan->status_tagihan == 'Lunas')
                            <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-emerald-100 text-emerald-700 uppercase">Lunas</span>
                        @elseif($tagihan->status_tagihan == 'Menunggu Verifikasi')
                            <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-amber-100 text-amber-700 uppercase">Menunggu</span>
                        @else
                            <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-rose-100 text-rose-700 uppercase">Belum Bayar</span>
                        @endif
                    </div>
                    
                    <p class="text-xs text-slate-400 mb-1">Total Tagihan</p>
                    <p class="text-lg font-bold text-slate-900 mb-6">Rp {{ number_format($tagihan->jumlah_bayar, 0, ',', '.') }}</p>
                </div>

                @if($tagihan->status_tagihan != 'Lunas' && $tagihan->status_tagihan != 'Menunggu Verifikasi')
                    <a href="{{ url('ortu/bayar/' . $tagihan->id_detail) }}" 
                       class="block w-full text-center bg-blue-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                       Bayar Sekarang
                    </a>
                @else
                    <button disabled class="block w-full text-center bg-slate-100 text-slate-400 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed">
                        {{ $tagihan->status_tagihan == 'Lunas' ? 'Lunas' : 'Sedang Diproses' }}
                    </button>
                @endif
            </div>
        @empty
            <div class="col-span-full p-10 text-center text-slate-500 bg-white rounded-2xl border border-slate-100">
                Belum ada data tagihan untuk saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection