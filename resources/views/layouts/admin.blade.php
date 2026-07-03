<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TK Mutiara Bogor</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F5F7FA] text-slate-800 antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col justify-between p-6 shrink-0">
        <div>
            <div class="flex items-center gap-3 px-2 py-4 mb-6">
                <div class="bg-[#1E88E5] text-white p-2 rounded-xl flex items-center justify-center shadow-md shadow-blue-200">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 leading-tight">TK Mutiara</h1>
                    <span class="text-xs text-slate-400 font-medium tracking-wide">BOGOR</span>
                </div>
            </div>

            <!-- Menu Navigasi (Ganti di resources/views/layouts/admin.blade.php) -->
            <nav class="space-y-1.5">
                <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/dashboard') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="/admin/siswa" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/siswa') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Data Siswa</span>
                </a>

                <a href="/admin/tagihan" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/tagihan') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span>Kelola Tagihan</span>
                </a>

                <a href="/admin/verifikasi" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/verifikasi') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                    <span>Verifikasi Pembayaran</span>
                </a>

                <!-- MENU PENGUMUMAN (SUDAH AKTIF) -->
                <a href="/admin/pengumuman" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/pengumuman') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                    <span>Pengumuman</span>
                </a>

                <!-- MENU LAPORAN KEUANGAN (SUDAH AKTIF) -->
                <a href="/admin/laporan" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ Request::is('admin/laporan') ? 'text-emerald-800 bg-[#DFF7E5] font-semibold' : 'text-slate-500 hover:text-[#1E88E5] hover:bg-slate-50' }} rounded-xl transition-all">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    <span>Laporan Keuangan</span>
                </a>
            </nav>

      <form action="{{ route('logout') }}" method="POST" class="w-full mt-auto">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-all w-full text-left cursor-pointer">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white border-b border-slate-100 px-8 py-4 flex items-center justify-between shrink-0 sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-bold text-slate-900">@yield('page_title')</h2>
                <p class="text-xs text-slate-400 font-medium">Sistem Informasi Pembayaran Keuangan Sekolah</p>
            </div>

            <div class="flex items-center gap-5">
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900">Admin TK</p>
                        <p class="text-[11px] font-medium text-slate-400">Administrator</p>
                    </div>
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100" class="w-10 h-10 rounded-xl object-cover">
                </div>
            </div>
        </header>

        <main class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>