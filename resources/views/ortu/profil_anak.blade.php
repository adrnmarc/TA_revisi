@extends('layouts.ortu')
@section('header', 'Profil Anak')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .font-display { font-family: 'Baloo 2', system-ui, sans-serif; }
    .font-body    { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
</style>

<div class="font-body">

    <div class="flex items-center gap-2 mb-6">
        <h2 class="font-display text-2xl font-bold text-slate-800 tracking-tight">Detail Profil Siswa</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Profil Utama --}}
        <div class="lg:col-span-2 relative bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">

            {{-- header hijau kecil ala kop kartu pelajar --}}
            <div class="bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-8 pt-8 pb-14 text-white relative">
                <svg class="absolute right-4 top-4 opacity-20" width="70" height="70" viewBox="0 0 24 24" fill="white">
                    <circle cx="12" cy="12" r="12"/>
                </svg>
                <p class="text-xs uppercase tracking-[3px] font-bold text-emerald-100">Kartu Siswa</p>
                <h3 class="font-display text-lg font-bold mt-1">TK Mutiara Bogor</h3>
            </div>

            <div class="px-8 -mt-10 pb-8">

                <div class="flex items-end gap-5">

                    <div class="relative shrink-0">
                        <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('images/default-avatar.jpg') }}"
                             class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-lg bg-slate-100">
                        <span class="absolute -bottom-2 -right-2 bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-base border-2 border-white shadow-sm">
                            🎒
                        </span>
                    </div>

                    <div class="pb-1">
                        <h1 class="font-display text-2xl font-bold text-slate-900">{{ $siswa->nama }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="bg-emerald-50 text-emerald-700 font-bold px-3 py-1 rounded-full text-xs">
                                {{ $siswa->kelas }}
                            </span>
                            <span class="bg-slate-50 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold text-slate-500 uppercase tracking-widest">
                                NIS: {{ $siswa->nis }}
                            </span>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-2 gap-4 mt-8">

                    <div class="relative bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <span class="absolute -top-2 left-4 w-8 h-3 bg-amber-300 rounded-sm rotate-[-4deg] opacity-90"></span>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Tanggal Lahir</p>
                        <p class="font-display font-bold text-slate-800 mt-1">
                            {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>

                    <div class="relative bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <span class="absolute -top-2 left-4 w-8 h-3 bg-sky-300 rounded-sm rotate-[-4deg] opacity-90"></span>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Jenis Kelamin</p>
                        <p class="font-display font-bold text-slate-800 mt-1">{{ $siswa->jenis_kelamin ?? '-' }}</p>
                    </div>

                </div>

                <div class="relative bg-slate-50 p-4 rounded-2xl border border-slate-100 mt-4">
                    <span class="absolute -top-2 left-4 w-8 h-3 bg-rose-300 rounded-sm rotate-[-4deg] opacity-90"></span>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Alamat Lengkap</p>
                    <p class="font-semibold text-slate-700 mt-1 leading-relaxed">{{ $siswa->alamat ?? '-' }}</p>
                </div>

            </div>

        </div>

        {{-- Kolom Kanan: Info Orang Tua --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">

            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    👨‍👩‍👧
                </div>
                <h3 class="font-display font-bold text-slate-800 text-lg">Orang Tua</h3>
            </div>

            <div class="space-y-5">

                <div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Orang Tua / Wali</p>
                    <p class="font-display font-bold text-lg text-slate-900 mt-1">{{ $siswa->nama_orangtua ?? '-' }}</p>
                </div>

                <div class="pt-5 border-t border-dashed border-slate-200">
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Kontak HP</p>
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $siswa->kontak ?? '') }}" target="_blank"
                       class="inline-flex items-center gap-2 font-display font-bold text-emerald-600 mt-1 hover:underline">
                        📱 {{ $siswa->kontak ?? '-' }}
                    </a>
                </div>

                <div class="pt-5 border-t border-dashed border-slate-200">
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Wali Kelas</p>
                    <p class="font-bold text-slate-700 mt-1">{{ $siswa->wali ?? '-' }}</p>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection