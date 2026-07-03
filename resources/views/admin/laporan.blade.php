@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan Keuangan')

@section('content')
    {{-- STATISTIK CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Total Pendapatan</span>
                <div class="p-2 bg-emerald-50 text-emerald-500 rounded-xl"><i data-lucide="trending-up" class="w-4 h-4"></i></div>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            <span class="text-[11px] font-semibold text-emerald-600 block mt-1">Uang masuk lunas</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Total Target</span>
                <div class="p-2 bg-blue-50 text-[#1E88E5] rounded-xl"><i data-lucide="dollar-sign" class="w-4 h-4"></i></div>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
            <span class="text-[11px] font-semibold text-slate-400 block mt-1">Akumulasi seluruh tagihan</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Transaksi Lunas</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
            </div>
            <h3 class="text-xl font-bold text-slate-800">{{ $siswaLunas }} <span class="text-xs font-medium text-slate-400">Transaksi</span></h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Belum Bayar</span>
                <div class="p-2 bg-rose-50 text-rose-500 rounded-xl"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>
            </div>
            <h3 class="text-xl font-bold text-slate-800">{{ $siswaBelumLunas }} <span class="text-xs font-medium text-slate-400">Transaksi</span></h3>
        </div>
    </div>

    {{-- FILTER & CETAK --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6 bg-white p-4 rounded-2xl border border-slate-100 items-end justify-between print:hidden">
        <form action="/admin/laporan" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">DARI TANGGAL</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="p-2 border rounded-xl w-40 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 block mb-1">SAMPAI TANGGAL</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="p-2 border rounded-xl w-40 text-sm">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-semibold cursor-pointer">Filter</button>
            <a href="/admin/laporan" class="bg-slate-100 px-4 py-2 rounded-xl font-semibold cursor-pointer">Reset</a>
        </form>

        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl font-semibold flex items-center gap-2 cursor-pointer transition-all">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak Laporan</span>
        </button>
    </div>

   <a href="/admin/laporan/export?start_date={{request('start_date')}}&end_date={{request('end_date')}}" 
   target="_blank" 
   class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-semibold">
   Unduh PDF
    </a>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-slate-400"></i>
                <span>Riwayat Transaksi</span>
            </h4>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="py-4 px-6">Nama Siswa</th>
                    <th class="py-4 px-6">Jenis Tagihan</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6">Tanggal</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-100">
                @forelse($riwayatTransaksi as $tx)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-4 px-6 text-slate-900 font-semibold">{{ $tx->siswa->nama ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $tx->nama_iuran }}</td>
                        <td class="py-4 px-6 text-slate-900 font-semibold">Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg border {{ $tx->status_tagihan == 'Lunas' ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-rose-700 bg-rose-50 border-rose-100' }}">
                                {{ $tx->status_tagihan }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-500">{{ \Carbon\Carbon::parse($tx->tagihan->tanggal_tagihan ?? now())->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CSS PRINT --}}
    <style media="print">
        @page { size: landscape; }
        .print\:hidden { display: none !important; }
    </style>
@endsection