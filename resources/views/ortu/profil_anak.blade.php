@extends('layouts.ortu')
@section('header', 'Profil Anak')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Foto & Nama (Lebih ringkas) -->
        <div class="lg:col-span-4">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm text-center sticky top-8">
                <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('images/default-avatar.jpg') }}" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-slate-50 shadow-md mx-auto">
                <h1 class="text-2xl font-black text-slate-900 mt-6">{{ $siswa->nama }}</h1>
                <p class="text-emerald-600 font-bold text-sm mt-1">{{ $siswa->kelas }}</p>
                <div class="mt-4 inline-block bg-slate-100 px-4 py-1 rounded-full text-xs font-bold text-slate-600">
                    NIS: {{ $siswa->nis }}
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Data (Dibagi dua kartu yang sejajar) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Data Pribadi
                </h3>
                <div class="grid grid-cols-2 gap-y-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Tanggal Lahir</p>
                        <p class="font-bold text-slate-800">{{ $siswa->tanggal_lahir ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Jenis Kelamin</p>
                        <p class="font-bold text-slate-800">{{ $siswa->jenis_kelamin ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Alamat</p>
                        <p class="font-semibold text-slate-700">{{ $siswa->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Orang Tua -->
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                    Informasi Orang Tua
                </h3>
                <div class="grid grid-cols-2 gap-y-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Nama Wali</p>
                        <p class="font-bold text-slate-800">{{ $siswa->nama_orangtua ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Kontak HP</p>
                        <p class="font-bold text-indigo-600">{{ $siswa->kontak ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection