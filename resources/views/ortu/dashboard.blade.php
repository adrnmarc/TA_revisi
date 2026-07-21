@extends('layouts.ortu')
@section('header','Dashboard Orang Tua')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght=500;600;700;800&family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

    .font-display { font-family: 'Baloo 2', system-ui, sans-serif; }
    .font-body    { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
</style>

<div class="font-body space-y-8 bg-[#F6F3EC] -m-6 p-6 rounded-3xl">

    {{-- HERO WELCOME CARD --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 p-8 text-white shadow-xl">

        {{-- decorative paper-cutout shapes --}}
        <svg class="absolute -right-6 -top-10 opacity-20" width="180" height="180" viewBox="0 0 24 24" fill="white">
            <path d="M12 .5l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9-6.3 3.9 1.7-7L1.9 7.7l7.1-.6z"/>
        </svg>
        <svg class="absolute right-24 bottom-4 opacity-10" width="90" height="90" viewBox="0 0 24 24" fill="white">
            <circle cx="12" cy="12" r="12"/>
        </svg>
        <svg class="absolute right-2 bottom-10 opacity-20" width="40" height="40" viewBox="0 0 24 24" fill="white">
            <path d="M12 .5l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.9-6.3 3.9 1.7-7L1.9 7.7l7.1-.6z"/>
        </svg>

        <div class="relative">

            <p class="uppercase tracking-[4px] text-emerald-100 text-xs font-bold">
                Portal Orang Tua
            </p>

            <h2 class="font-display text-4xl font-extrabold mt-3">
                Halo, Bapak/Ibu {{ $siswa->nama_orangtua }} 👋
            </h2>

            <p class="mt-3 text-emerald-100 text-lg">
                Terima kasih telah menggunakan Portal Pembayaran
                <b>TK Mutiara Bogor.</b>
            </p>

        </div>

    </div>


    {{-- PROFIL ANAK — gaya "kartu pelajar" / tiket --}}
    <div class="relative bg-white rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch overflow-hidden">

        <div class="md:w-56 bg-gradient-to-br from-emerald-50 to-teal-50 flex flex-col items-center justify-center gap-3 p-6 text-center">

            <div class="w-20 h-20 rounded-full bg-white shadow-inner border-4 border-white ring-2 ring-emerald-200 flex items-center justify-center font-display text-3xl font-bold text-emerald-600">
                {{ strtoupper(substr($siswa->nama,0,1)) }}
            </div>

            <p class="text-[10px] uppercase tracking-widest font-bold text-emerald-500">
                Kartu Siswa
            </p>

        </div>

        {{-- garis putus + notch tiket --}}
        <div class="hidden md:block relative w-px border-l-2 border-dashed border-slate-200">
            <span class="absolute -left-[9px] -top-3 w-4 h-4 rounded-full bg-[#F6F3EC]"></span>
            <span class="absolute -left-[9px] -bottom-3 w-4 h-4 rounded-full bg-[#F6F3EC]"></span>
        </div>

        <div class="flex-1 p-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                
                {{-- Modifikasi Nama Lengkap & Nama Panggilan Anak --}}
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h3 class="font-display text-xl font-bold text-slate-800">
                        {{ $siswa->nama }}
                    </h3>
                    @if($siswa->nama_panggilan)
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100 font-body">
                            ({{ $siswa->nama_panggilan }})
                        </span>
                    @endif
                </div>


            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-5">

                <div>
                    <div class="text-xs uppercase font-bold text-slate-400">NIS</div>
                    <div class="font-display font-bold text-slate-700 mt-1">{{ $siswa->nis }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase font-bold text-slate-400">Kelas</div>
                    <div class="font-display font-bold text-slate-700 mt-1">{{ $siswa->kelas }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase font-bold text-slate-400">Orang Tua</div>
                    <div class="font-display font-bold text-slate-700 mt-1">{{ $siswa->nama_orangtua }}</div>
                </div>

            </div>

        </div>

    </div>


    {{-- STATISTIK — kartu dengan aksen washi tape --}}
    @php
        $totalTagihan = $riwayat->sum('jumlah_bayar');
        $sudahDibayar = $riwayat->where('status_tagihan','Lunas')->sum('jumlah_bayar');
        $belumDibayar = $totalTagihan - $sudahDibayar;
        $progress     = $totalTagihan > 0 ? round(($sudahDibayar / $totalTagihan) * 100) : 0;

        $stats = [
            ['label'=>'Total Tagihan','value'=>'Rp '.number_format($totalTagihan,0,',','.'),'icon'=>'💰','tape'=>'bg-amber-300','text'=>'text-slate-800'],
            ['label'=>'Sudah Dibayar','value'=>'Rp '.number_format($sudahDibayar,0,',','.'),'icon'=>'✅','tape'=>'bg-emerald-300','text'=>'text-emerald-600'],
            ['label'=>'Belum Dibayar','value'=>'Rp '.number_format($belumDibayar,0,',','.'),'icon'=>'⏳','tape'=>'bg-rose-300','text'=>'text-rose-500'],
            ['label'=>'Jumlah Transaksi','value'=>$jumlahTransaksi,'icon'=>'📊','tape'=>'bg-sky-300','text'=>'text-sky-600'],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-2">

        @foreach($stats as $s)

        <div class="relative bg-white rounded-3xl p-6 pt-7 shadow-sm border border-slate-100">

            {{-- washi tape --}}
            <span class="absolute -top-2 left-6 w-10 h-4 {{ $s['tape'] }} rounded-sm rotate-[-4deg] opacity-90 shadow-sm"></span>

            <div class="flex items-center justify-between">
                <p class="text-xs uppercase font-bold text-slate-400">{{ $s['label'] }}</p>
                <span class="text-lg">{{ $s['icon'] }}</span>
            </div>

            <h2 class="font-display text-2xl font-bold {{ $s['text'] }} mt-3">
                {{ $s['value'] }}
            </h2>

        </div>

        @endforeach

    </div>


    {{-- PROGRESS BAR — bintang berjalan --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-slate-700">
                Progress Pembayaran
            </h3>
            <span class="font-display font-bold text-emerald-600">
                {{ $progress }}%
            </span>
        </div>

        <div class="relative w-full h-4 bg-slate-100 rounded-full overflow-visible">

            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500"
                 style="width: {{ $progress }}%">
            </div>

            <span class="absolute -top-3 text-lg leading-none transition-all duration-500"
                  style="left: calc({{ $progress }}% - 10px)">
                🌟
            </span>

        </div>

        <p class="text-sm text-slate-400 mt-4">
            Rp {{ number_format($sudahDibayar,0,',','.') }} dari
            Rp {{ number_format($totalTagihan,0,',','.') }} tagihan telah dibayar.
        </p>

    </div>


    {{-- QUICK ACTION --}}
    <div class="grid md:grid-cols-3 gap-5">

        <a href="/ortu/tagihan"
            class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-3xl">💳</div>
            <div>
                <h3 class="font-display font-bold text-lg">Bayar Tagihan</h3>
                <p class="text-slate-500 text-sm mt-1">Lihat & bayar tagihan aktif.</p>
            </div>
        </a>

        <a href="/riwayat"
            class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center text-3xl">📜</div>
            <div>
                <h3 class="font-display font-bold text-lg">Riwayat</h3>
                <p class="text-slate-500 text-sm mt-1">Histori seluruh pembayaran.</p>
            </div>
        </a>

        <a href="/ortu/profil"
            class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-3xl">👦</div>
            <div>
                <h3 class="font-display font-bold text-lg">Profil Anak</h3>
                <p class="text-slate-500 text-sm mt-1">Informasi lengkap siswa.</p>
            </div>
        </a>

    </div>


    {{-- RIWAYAT PEMBAYARAN --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100">

        <div class="border-b p-6 flex items-center justify-between">
            <h2 class="font-display font-bold text-xl">Riwayat Pembayaran Terbaru</h2>
            <a href="/riwayat" class="text-emerald-600 font-semibold text-sm hover:underline">
                Lihat Semua
            </a>
        </div>

        <div class="divide-y">

            @forelse($riwayat->take(5) as $item)

            <div class="p-5 flex justify-between items-center hover:bg-slate-50 transition rounded-2xl">

                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center text-xl shrink-0">🧾</div>
                    <div>
                        <div class="font-bold text-slate-800">{{ $item->nama_iuran }}</div>
                        <div class="text-sm text-slate-400">{{ $item->created_at->format('d F Y') }}</div>
                    </div>
                </div>

                <div class="text-right">

                    <div class="font-display font-bold text-lg text-slate-800">
                        Rp {{ number_format($item->jumlah_bayar,0,',','.') }}
                    </div>

                    @if($item->status_tagihan=="Lunas")
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Lunas</span>
                    @elseif($item->status_tagihan=="Menunggu Verifikasi")
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Menunggu Verifikasi</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-600 text-xs font-bold">Belum Dibayar</span>
                    @endif

                </div>

            </div>

            @empty

            <div class="py-16 text-center">
                <div class="text-6xl mb-4">📄</div>
                <h3 class="font-display font-bold text-slate-700">Belum Ada Riwayat Pembayaran</h3>
                <p class="mt-2 text-slate-400 text-sm max-w-xs mx-auto">
                    Semua transaksi pembayaran akan muncul di sini setelah kamu melakukan pembayaran pertama.
                </p>
            </div>

            @endforelse

        </div>

    </div>


    {{-- PENGUMUMAN — papan gabus / corkboard --}}
    @php
        $tapeColors  = ['bg-amber-200','bg-sky-200','bg-rose-200','bg-emerald-200'];
        $noteColors  = ['bg-amber-50','bg-sky-50','bg-rose-50','bg-emerald-50'];
        $rotations   = ['-rotate-2','rotate-1','-rotate-1','rotate-2'];
    @endphp

    <div>

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display text-xl font-bold">📌 Papan Pengumuman</h2>
            <a href="/ortu/pengumuman" class="text-emerald-600 font-semibold hover:underline text-sm">
                Lihat Semua
            </a>
        </div>

        <div class="rounded-[2rem] p-8 bg-[#EFE3CC] border-2 border-dashed border-[#D8C7A3]">

            <div class="grid md:grid-cols-3 gap-8">

                @forelse($pengumuman as $item)

                @php
                    $i = $loop->iteration % 4;
                @endphp

                <div class="relative {{ $rotations[$i] }} hover:rotate-0 transition duration-300">

                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full {{ $tapeColors[$i] }} shadow-md border-2 border-white z-10"></span>

                    <div class="{{ $noteColors[$i] }} rounded-xl shadow-lg p-6 min-h-[160px]">

                        <div class="text-xs uppercase font-bold text-slate-500">
                            {{ $item->created_at->format('d M Y') }}
                        </div>

                        <h3 class="mt-2 font-display text-lg font-bold text-slate-800">
                            {{ $item->judul }}
                        </h3>

                        <p class="mt-2 text-slate-600 text-sm line-clamp-3">
                            {{ $item->isi }}
                        </p>

                    </div>

                </div>

                @empty

                <div class="md:col-span-3 text-center py-10">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="font-display font-bold text-slate-700">Belum Ada Pengumuman</h3>
                    <p class="mt-2 text-slate-500 text-sm">Pengumuman dari sekolah akan tampil di sini.</p>
                </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection