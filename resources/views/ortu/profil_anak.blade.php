@extends('layouts.ortu')
@section('header', 'Profil Anak')
@section('content')
<h2 class="text-2xl font-extrabold text-slate-800 mb-6 tracking-tight">Detail Profil Siswa</h2>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Profil Utama -->
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-blue-50 p-8 rounded-3xl border border-slate-100 shadow-lg shadow-blue-100/50">
        <div class="flex items-center gap-6">
            <div class="relative">
                <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('images/default-avatar.jpg') }}" 
     class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-md">
                <span class="absolute -top-2 -right-2 bg-blue-500 text-white p-1 rounded-full"><i class="fas fa-child"></i></span>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900">{{ $siswa->nama }}</h1>
                <p class="text-blue-600 font-bold bg-blue-100 px-3 py-1 rounded-full inline-block text-sm mt-1">{{ $siswa->kelas }}</p>
                <div class="mt-3">
                    <span class="bg-white border px-3 py-1 rounded-lg text-xs font-bold text-slate-500 uppercase tracking-widest shadow-sm">NIS: {{ $siswa->nis }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Tanggal Lahir</p>
                <p class="font-bold text-slate-800 mt-1">{{ $siswa->tanggal_lahir ?? '-' }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Jenis Kelamin</p>
                <p class="font-bold text-slate-800 mt-1">{{ $siswa->jenis_kelamin ?? '-' }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mt-4">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Alamat Lengkap</p>
            <p class="font-semibold text-slate-700 mt-1 leading-relaxed">{{ $siswa->alamat ?? '-' }}</p>
        </div>
    </div>

    <!-- Kolom Kanan: Info Wali -->
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg shadow-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <h3 class="font-black text-slate-800 text-lg">Orang Tua</h3>
        </div>
        
        <div class="space-y-4">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Orang Tua / Wali</p>
                <p class="font-bold text-lg text-slate-900">{{ $siswa->nama_orangtua ?? '-' }}</p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Kontak HP</p>
                <p class="font-bold text-indigo-600 mt-1">{{ $siswa->kontak ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection