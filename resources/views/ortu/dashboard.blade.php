@extends('layouts.ortu')
@section('header', 'Dashboard Orangtua')

@section('content')
<div class="space-y-8">
    
    <!-- 1. HEADER & INFORMASI ANAK -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Halo, Ayah/Bunda</h2>
            <p class="text-slate-500 font-medium">Pantau progres pembayaran sekolah {{ $siswa->nama ?? 'Ananda' }} di sini.</p>
        </div>
        <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">Kelas {{ $siswa->kelas ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- 2. STATISTIK (Dibuat lebih modern) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kartu Saldo -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 p-8 rounded-3xl text-white shadow-xl shadow-emerald-200 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-widest mb-2">Total Dana Disetor</p>
                <h3 class="text-4xl font-black">Rp {{ number_format($totalBayar, 0, ',', '.') }}</h3>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-emerald-500/50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
        </div>
        <!-- Kartu Transaksi -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-1">Jumlah Transaksi</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $riwayat->count() }} Kali</h3>
            </div>
            <div class="bg-slate-100 p-4 rounded-2xl text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
    </div>

    <!-- 3. RIWAYAT & PENGUMUMAN -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <h3 class="font-bold text-slate-800 mb-6">Riwayat Pembayaran Terbaru</h3>
            <div class="space-y-6">
                @forelse($riwayat->take(4) as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.65 0-3 .89-3 2s1.35 2 3 2 3 .89 3 2-1.35 2-3 2m0-8c1.11 0 2.08.4 2.59 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.4-2.59-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $item->nama_iuran }}</p>
                            <p class="text-[11px] text-slate-400 font-medium">{{ $item->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $item->status_tagihan == 'Lunas' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $item->status_tagihan }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm italic">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>

        <!-- Pengumuman -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <h3 class="font-bold text-slate-800 mb-6">Pengumuman</h3>
            <div class="space-y-4">
                @foreach($pengumuman as $p)
                <div class="group cursor-pointer">
                    <h4 class="text-sm font-bold text-slate-700 group-hover:text-emerald-600 transition">{{ $p->judul }}</h4>
                    <p class="text-xs text-slate-400 mt-1">{{ $p->created_at->diffForHumans() }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection