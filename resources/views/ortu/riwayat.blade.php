@extends('layouts.ortu')
@section('header', 'Riwayat Pembayaran')

@section('content')
<div class="p-8 bg-white rounded-2xl shadow-sm border border-slate-200">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-6 bg-white border border-slate-200 rounded-xl">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="p-6 bg-emerald-500 border border-emerald-600 rounded-xl">
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Sudah Dibayar</p>
            <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</p>
        </div>
        <div class="p-6 bg-amber-600 border border-amber-700 rounded-xl">
            <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Belum Dibayar</p>
            <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($belumDibayar, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Jenis Tagihan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Nominal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($riwayat as $item)
            <tr>
                <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-slate-800">{{ $item->nama_iuran }}</td>
                
                <td class="px-6 py-4 text-sm text-slate-600">
                    @if(str_contains(strtolower($item->nama_iuran), 'program'))
                        <div class="font-semibold text-slate-800">
                            Rp {{ number_format($item->total_dibayar, 0, ',', '.') }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            dari total Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                        </div>
                    @else
                        Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    @if($item->status_tagihan == 'Lunas')
                        <span class="px-3 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full border border-emerald-200">LUNAS</span>
                    @else
                        <span class="px-3 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full border border-amber-200">VERIFIKASI</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat transaksi.</td>
            </tr>
            @endforelse
        </tbody>
        </table>
    </div>
</div>
@endsection