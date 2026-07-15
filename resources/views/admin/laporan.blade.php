@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan Keuangan')

@section('content')
<div class="print-container">
    {{-- JUDUL LAPORAN (Hanya muncul saat dicetak) --}}
    <div class="hidden print:block mb-8 text-center">
        <h1 class="text-2xl font-bold uppercase underline">Laporan Keuangan TK Mutiara</h1>
        <p class="text-sm text-slate-500">Periode: {{ request('start_date') ?: 'Semua' }} s/d {{ request('end_date') ?: 'Semua' }}</p>
    </div>

    {{-- STATISTIK CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Pendapatan</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Target</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Transaksi Lunas</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">{{ $siswaLunas }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Belum Bayar</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">{{ $siswaBelumLunas }}</h3>
        </div>
    </div>

    {{-- FILTER & CETAK --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6 bg-white p-4 rounded-2xl border border-slate-100 items-end justify-between print:hidden">
        <form action="/admin/laporan" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-[10px] font-bold text-slate-400 block mb-1">DARI</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="p-2 border rounded-xl text-sm">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 block mb-1">SAMPAI</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="p-2 border rounded-xl text-sm">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-semibold text-sm">Filter</button>
            <a href="/admin/laporan" class="bg-slate-100 px-4 py-2 rounded-xl font-semibold text-sm">Reset</a>
        </form>

        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-slate-800 text-white px-6 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 hover:bg-slate-900 transition">
                Cetak Laporan
            </button>
            <a href="/admin/laporan/export?start_date={{request('start_date')}}&end_date={{request('end_date')}}" target="_blank" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 hover:bg-emerald-700 transition">
                Unduh PDF
            </a>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="py-4 px-6">Nama Siswa</th>
                    <th class="py-4 px-6">Jenis Tagihan</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6">Tanggal Jatuh Tempo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($riwayatTransaksi as $tx)
                    <tr class="text-sm">
                        <td class="py-4 px-6 font-semibold text-slate-900">{{ $tx->siswa->nama ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $tx->nama_iuran }}</td>
                        <td class="py-4 px-6 font-semibold">Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-md {{ $tx->status_tagihan == 'Lunas' ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }}">
                                {{ $tx->status_tagihan }}
                            </span>
                        </td>
                        {{-- Tanggal disinkronkan ke jatuh_tempo --}}
                        <td class="py-4 px-6 text-slate-600 font-medium">
                            {{ $tx->tagihan ? \Carbon\Carbon::parse($tx->tagihan->jatuh_tempo)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-400">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CSS KHUSUS CETAK --}}
<style media="print">
    @page { size: landscape; margin: 1cm; }
    /* Sembunyikan elemen dashboard/admin saat dicetak */
    nav, aside, header, .print\:hidden { display: none !important; }
    /* Pastikan container terlihat penuh */
    .print-container { width: 100% !important; margin: 0 !important; }
    /* Styling tabel agar rapi dan terlihat garisnya di kertas */
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #cbd5e1; padding: 12px; }
    .bg-slate-50 { background-color: #f1f5f9 !important; }
</style>
@endsection