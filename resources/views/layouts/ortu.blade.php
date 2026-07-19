<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Orang Tua - TK Mutiara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-64 flex-shrink-0 bg-white border-r border-slate-100 flex flex-col">
            <div class="p-8">
                <h1 class="text-2xl font-extrabold text-emerald-600 tracking-tight">TK Mutiara</h1>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Portal Orang Tua</span>
            </div>
            
            <nav class="flex-grow px-4 space-y-1">
                @php
                    $menu = [
                        ['url' => '/ortu/dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['url' => route('ortu.profil'), 'label' => 'Profil Anak', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['url' => '/ortu/tagihan', 'label' => 'Tagihan Saya', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['url' => route('riwayat'), 'label' => 'Riwayat Pembayaran', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['url' => '/ortu/pengumuman', 'label' => 'Pengumuman', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                    ];
                @endphp

                @foreach($menu as $item)
                    @php $isActive = request()->fullUrlIs(url($item['url']) . '*'); @endphp
                    <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $isActive ? 'bg-emerald-50 text-emerald-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        <svg class="w-5 h-5 {{ $isActive ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" /></svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            
            <div class="p-6 border-t border-slate-100">
                <form action="{{ route('logout.ortu') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-3 w-full px-4 py-3 text-slate-400 font-medium hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Header yang tetap di atas -->
            <header class="bg-white border-b border-slate-100 py-6 px-10 flex justify-between items-center z-10">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">@yield('header')</h2>
                    <p class="text-slate-400 text-sm">Selamat datang di Portal Orang Tua</p>
                </div>
            </header>
            
            <!-- Konten yang bisa di-scroll secara mandiri -->
            <div class="flex-1 overflow-y-auto p-10 bg-slate-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>
</html>