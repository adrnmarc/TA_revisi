@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Admin')

@section('content')
    {{-- Perhitungan Persentase Pembayaran Lunas Secara Dinamis --}}
    @php
        $totalTagihan = $tagihanLunas + $menungguVerifikasi;
        $persentaseLunas = $totalTagihan > 0 ? round(($tagihanLunas / $totalTagihan) * 100) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        {{-- Card 1: Total Siswa Aktif --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-100 flex items-center justify-between shadow-xs">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">Total Siswa Aktif</span>
                <h3 class="text-2xl font-bold text-slate-900">{{ $totalSiswa }} Anak</h3>
            </div>
            <div class="bg-blue-50 text-[#1E88E5] p-3 rounded-xl"><i data-lucide="user" class="w-6 h-6"></i></div>
        </div>

        {{-- Card 2: Total Pendapatan --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-100 flex items-center justify-between shadow-xs">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">Total Pendapatan</span>
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-xl"><i data-lucide="wallet" class="w-6 h-6"></i></div>
        </div>

        {{-- Card 3: Pembayaran Lunas dengan Progress Bar Dinamis --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-100 space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">Pembayaran Lunas</span>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $persentaseLunas }}%</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">{{ $tagihanLunas }} Tagihan</h3>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $persentaseLunas }}%"></div>
            </div>
        </div>

        {{-- Card 4: Menunggu Verifikasi --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-100 flex items-center justify-between shadow-xs">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">Menunggu Verifikasi</span>
                <h3 class="text-2xl font-bold text-slate-900">{{ $menungguVerifikasi }} Transaksi</h3>
            </div>
            <div class="bg-amber-50 text-amber-500 p-3 rounded-xl {{ $menungguVerifikasi > 0 ? 'animate-pulse' : '' }}">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    {{-- Tabel Aktivitas Pembayaran Terbaru --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-base">Aktivitas Pembayaran Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Siswa</th>
                        <th class="py-4 px-6">Jenis Tagihan</th>
                        <th class="py-4 px-6">Nominal</th>
                        <th class="py-4 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-100">
                    @forelse($aktivitasTerbaru as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-slate-900 font-semibold">{{ $item->siswa->nama ?? 'Tidak Diketahui' }}</td>
                            <td class="py-4 px-6 text-slate-500 font-normal">{{ $item->nama_iuran }}</td>
                            <td class="py-4 px-6 text-slate-900 font-semibold">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                @if($item->status_tagihan == 'Lunas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-[#DFF7E5] text-emerald-800">Lunas</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-800">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="bar-chart-2" class="w-8 h-8 text-slate-300"></i>
                                    <span>Belum ada aktivitas pembayaran terbaru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection