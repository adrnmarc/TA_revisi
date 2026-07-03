@extends('layouts.ortu')
@section('header', 'Dashboard Orang Tua')

@section('content')
<div class="space-y-6"> 
    <!-- Informasi Anak -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-6">
        <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-400 font-bold uppercase tracking-wider">Informasi Anak</p>
            <h3 class="text-xl font-black text-slate-800">
                {{ $siswa->nama ?? 'Ananda' }} | NIS: {{ session('nis') }} | Kelas: {{ $siswa->kelas ?? 'N/A' }}
            </h3>
        </div>
    </div>

    <!-- Statistik Pembayaran -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase">Total Pembayaran Berhasil</p>
            <p class="text-3xl font-black text-indigo-600 mt-1">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase">Jumlah Transaksi</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $riwayat->count() }} Transaksi</p>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h3 class="font-black text-lg text-slate-800">Riwayat Pembayaran Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Iuran</th>
                        <th class="py-4 px-6">Nominal</th>
                        <th class="py-4 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6 font-medium text-slate-600">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="py-4 px-6 font-bold text-slate-800">{{ $item->nama_iuran }}</td>
                        <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $item->status_tagihan == 'Lunas' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                                {{ $item->status_tagihan }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-400">Belum ada data transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pengumuman -->
    <div>
        <h3 class="font-black text-lg text-slate-800 mb-4">Pengumuman Terbaru</h3>
        <div class="grid gap-4">
            @forelse($pengumuman as $p)
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:border-indigo-100 transition-colors">
                <h4 class="font-bold text-slate-800">{{ $p->judul }}</h4>
                <p class="text-sm text-slate-600 mt-1">{{ $p->isi }}</p>
                <p class="text-xs text-slate-400 mt-3 font-medium">{{ $p->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-slate-400">
                Belum ada pengumuman saat ini.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection