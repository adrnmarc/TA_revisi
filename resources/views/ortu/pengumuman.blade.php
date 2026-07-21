@extends('layouts.ortu')
@section('header', 'Pengumuman')

@section('content')

{{-- Font ceria untuk judul, tetap gampang dibaca untuk isi --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    .font-mading { font-family: 'Baloo 2', cursive; }
    .font-isi { font-family: 'Nunito', sans-serif; }

    .papan-mading {
        background-color: #E9D9B6;
        background-image:
            radial-gradient(circle, rgba(0,0,0,0.06) 1px, transparent 1px);
        background-size: 14px 14px;
        border: 3px dashed #C9AD7F;
    }

    .sticky-note {
        transition: transform .2s ease, box-shadow .2s ease;
        box-shadow: 3px 4px 0 rgba(0,0,0,0.06);
    }
    .sticky-note:hover {
        transform: rotate(0deg) scale(1.02) !important;
        box-shadow: 4px 8px 16px rgba(0,0,0,0.12);
        z-index: 10;
    }
    .sticky-note:nth-child(3n+1) { transform: rotate(-1.5deg); }
    .sticky-note:nth-child(3n+2) { transform: rotate(1deg); }
    .sticky-note:nth-child(3n+3) { transform: rotate(-0.5deg); }

    .pushpin {
        width: 18px; height: 18px;
        border-radius: 50%;
        box-shadow: 0 2px 3px rgba(0,0,0,0.25), inset 0 -2px 2px rgba(0,0,0,0.15), inset 0 2px 2px rgba(255,255,255,0.4);
    }

    @keyframes bob {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .bob { animation: bob 3s ease-in-out infinite; }
</style>

<div class="pt-8 px-8 pb-12" style="background-color:#FDF6EC;">
    <div class="max-w-4xl mx-auto">

        {{-- Header ceria --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="text-3xl bob">📌</span>
            <div>
                <h1 class="font-mading font-extrabold text-2xl text-slate-700">Papan Mading Sekolah</h1>
                <p class="font-isi text-sm text-slate-400">Kabar dan info seru buat Bapak/Ibu, ditempel langsung dari sekolah!</p>
            </div>
        </div>

        {{-- Papan gabus / corkboard --}}
        <div class="papan-mading rounded-[2rem] p-6 md:p-10 shadow-inner">

            @forelse($pengumuman as $item)
                @php
                    // warna sticky note sekarang ngikutin kategori, bukan urutan loop lagi,
                    // biar pengumuman "Penting" selalu mencolok warnanya di mading manapun ia berada
                    $warnaSet = [
                        'umum'       => ['bg' => '#FFF3B0', 'pin' => '#F59E0B'], // kuning
                        'kegiatan'   => ['bg' => '#BFE9E0', 'pin' => '#0FA968'], // mint
                        'penting'    => ['bg' => '#FFD3E0', 'pin' => '#F472B6'], // pink
                        'pembayaran' => ['bg' => '#C9E4FF', 'pin' => '#60A5FA'], // biru
                        'libur'      => ['bg' => '#E4D6FF', 'pin' => '#A78BFA'], // ungu
                    ];
                    $warna = $warnaSet[$item->kategori] ?? $warnaSet['umum'];
                @endphp
                <div class="sticky-note relative rounded-2xl p-5 mb-8 last:mb-0 inline-block w-full"
                     style="background-color: {{ $warna['bg'] }};">

                    {{-- pushpin --}}
                    <span class="pushpin absolute -top-2 left-1/2 -translate-x-1/2" style="background-color: {{ $warna['pin'] }};"></span>

                    <div class="flex justify-between items-start gap-3 mb-2">
                        <h4 class="font-mading font-bold text-lg text-slate-700">{{ $item->judul }}</h4>
                        <span class="flex-shrink-0 font-isi text-[10px] font-bold text-slate-500 uppercase tracking-wide bg-white/70 px-2.5 py-1 rounded-full">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </span>
                    </div>
                    <p class="font-isi text-sm text-slate-600 leading-relaxed">
                        {{ $item->isi }}
                    </p>
                </div>
            @empty
                <div class="text-center py-16">
                    <span class="text-5xl block mb-3">🎈</span>
                    <p class="font-mading font-bold text-slate-600 text-lg mb-1">Mading masih kosong nih!</p>
                    <p class="font-isi text-sm text-slate-500">Pengumuman baru bakal nempel di sini ya, Bapak/Ibu.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection