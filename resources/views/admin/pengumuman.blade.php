@extends('layouts.admin')

@section('title', 'Pengumuman Sekolah')
@section('page_title', 'Pengumuman')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    .font-display { font-family: 'Space Grotesk', system-ui, sans-serif; }
    .font-body    { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
</style>

<div class="font-body">

    @if(session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display text-lg font-bold text-slate-900">Pengumuman Sekolah</h2>
            <p class="text-sm text-slate-400">{{ $daftarPengumuman->count() }} pengumuman diterbitkan</p>
        </div>

        <button id="btnBukaPengumuman" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-emerald-100 transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Pengumuman</span>
        </button>
    </div>

    <div class="relative bg-emerald-50/50 border border-emerald-100 p-6 rounded-3xl space-y-4 overflow-hidden">

        <span class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500"></span>

        <div class="flex items-center gap-2 text-emerald-700 font-display font-bold text-sm mb-2">
            <i data-lucide="megaphone" class="w-5 h-5"></i>
            <span>Pengumuman Terbaru</span>
        </div>

        @forelse($daftarPengumuman as $info)

        <div class="relative bg-white p-5 pl-6 rounded-2xl border border-slate-100 shadow-xs flex items-start justify-between gap-4 transition-all hover:shadow-md overflow-hidden">

            <span class="absolute left-0 top-0 h-full w-1 bg-emerald-400"></span>

            <div class="flex items-start gap-4">
                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                    <i data-lucide="info" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-display font-bold text-slate-800 text-sm sm:text-base mb-1">{{ $info->judul }}</h4>
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-400 mb-2 font-semibold">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        {{ \Carbon\Carbon::parse($info->tanggal)->format('d F Y') }}
                    </span>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ $info->isi }}</p>
                </div>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                <form action="/admin/pengumuman/{{ $info->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

        </div>

        @empty

        <div class="bg-white p-10 text-center text-slate-400 rounded-2xl border-2 border-dashed border-slate-200">
            <div class="flex flex-col items-center justify-center gap-2">
                <i data-lucide="bell-off" class="w-8 h-8 text-slate-300"></i>
                <span class="font-medium">Belum ada pengumuman yang diterbitkan saat ini.</span>
            </div>
        </div>

        @endforelse

    </div>

    {{-- MODAL TAMBAH PENGUMUMAN --}}
    <div id="modalPengumuman" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-800 text-sm">Buat Pengumuman Baru</h3>
                </div>
                <button id="btnTutupPengumuman" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="/admin/pengumuman" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Piknik Sekolah ke Kebun Raya"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Terbit</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Isi Pengumuman</label>
                    <textarea name="isi" required rows="4" placeholder="Tuliskan detail informasi pengumuman di sini..."
                        class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-4">
                    <button type="button" id="btnBatalPengumuman" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-100 transition-all">
                        Terbitkan
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal    = document.getElementById('modalPengumuman');
        const btnBuka  = document.getElementById('btnBukaPengumuman');
        const btnTutup = document.getElementById('btnTutupPengumuman');
        const btnBatal = document.getElementById('btnBatalPengumuman');

        btnBuka.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });

        function tutupModal() {
            modal.classList.add('hidden');
        }

        btnTutup.addEventListener('click', tutupModal);
        btnBatal.addEventListener('click', tutupModal);
    });

    if (window.lucide) lucide.createIcons();
</script>

@endsection