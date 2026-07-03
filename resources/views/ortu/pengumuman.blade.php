@extends('layouts.ortu') @section('header', 'Pengumuman')

@section('content')
    

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        @forelse($pengumuman as $item)
            <div class="flex gap-4 p-5 mb-4 last:mb-0 rounded-xl border border-slate-100 bg-slate-50 hover:border-emerald-100 transition-colors">
                <div class="flex-shrink-0 mt-1">
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                </div>
                
                <div class="flex-grow">
                    <div class="flex justify-between items-start">
                        <h4 class="text-base font-bold text-slate-800">{{ $item->judul }}</h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-white px-2 py-1 rounded border border-slate-100">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                        {{ $item->isi }}
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-slate-400">
                Belum ada pengumuman untuk saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection