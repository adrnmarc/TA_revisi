@extends('layouts.admin')

@section('title', 'Pengumuman Sekolah')
@section('page_title', 'Pengumuman')

@section('content')
    @if(session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="flex justify-end mb-6">
        <button id="btnBukaPengumuman" class="bg-[#1E88E5] hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-100 transition-all cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Pengumuman</span>
        </button>
    </div>

    <div class="bg-blue-50/70 border border-blue-100 p-6 rounded-3xl space-y-4">
        <div class="flex items-center gap-2 text-[#1E88E5] font-bold text-sm mb-2">
            <i data-lucide="megaphone" class="w-5 h-5"></i>
            <span>Pengumuman Terbaru</span>
        </div>

        @forelse($daftarPengumuman as $info)
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-start justify-between gap-4 transition-all hover:shadow-md">
                <div class="flex items-start gap-4">
                    <div class="p-2.5 rounded-xl bg-blue-50 text-[#1E88E5] shrink-0">
                        <i data-lucide="info" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm sm:text-base mb-1">{{ $info->judul }}</h4>
                        <span class="text-xs text-slate-400 block mb-2 font-semibold">
                            {{ \Carbon\Carbon::parse($info->tanggal)->format('d F Y') }}
                        </span>
                        <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ $info->isi }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-1 shrink-0">
                    <form action="/admin/pengumuman/{{ $info->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer">
                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 text-center text-slate-400 rounded-2xl border border-dashed border-slate-200">
                <div class="flex flex-col items-center justify-center gap-2">
                    <i data-lucide="bell-off" class="w-8 h-8 text-slate-300"></i>
                    <span>Belum ada pengumuman yang diterbitkan saat ini.</span>
                </div>
            </div>
        @endforelse
    </div>

    <div id="modalPengumuman" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm">Buat Pengumuman Baru</h3>
                <button id="btnTutupPengumuman" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="/admin/pengumuman" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Piknik Sekolah ke Kebun Raya" class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Tanggal Terbit</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Isi Pengumuman</label>
                    <textarea name="isi" required rows="4" placeholder="Tuliskan detail informasi pengumuman di sini..." class="w-full px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-4">
                    <button type="button" id="btnBatalPengumuman" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl cursor-pointer transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#1E88E5] hover:bg-blue-600 rounded-xl shadow-md shadow-blue-100 cursor-pointer transition-all">Terbitkan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById('modalPengumuman');
            const btnBuka = document.getElementById('btnBukaPengumuman');
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
    </script>
@endsection