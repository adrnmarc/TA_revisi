<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - TK Mutiara Bogor</title>
    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 antialiased relative overflow-hidden">

    <!-- EFEK CAHAYA GRADASI DI BACKROUND LAYAR UTAMA -->
    <div class="absolute inset-0 z-0 bg-cover bg-center opacity-10 mix-blend-overlay" 
         style="background-image: url('{{ asset('images/ilustrasi-spp.jpg') }}');">
    </div>
    <div class="absolute top-[-20%] left-[-10%] w-[550px] h-[550px] bg-blue-500/10 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[550px] h-[550px] bg-emerald-500/10 rounded-full blur-[130px] pointer-events-none"></div>

    <!-- MAIN CARD CONTAINER -->
    <div class="relative z-10 bg-white/95 backdrop-blur-md rounded-[32px] shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col md:flex-row p-4 min-h-[520px] gap-4 border border-white/20">

        <!-- SISI KIRI: BANNER GRADASI YANG LEBIH PREMIUM -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-[#1E88E5] via-[#1565C0] to-[#0D47A1] rounded-[24px] p-8 flex flex-col items-center justify-between text-center relative overflow-hidden min-h-[280px] md:min-h-auto text-white">
            <!-- Ornamen Lingkaran Glow -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-black/10 rounded-full blur-xl pointer-events-none"></div>

            <!-- Logo Sekolah (Menggunakan Logo Asli Kamu) -->
            <div class="mt-auto flex flex-col items-center">
                <div class="p-4 bg-white/10 rounded-2xl mb-4 backdrop-blur-sm shadow-inner border border-white/10">
                    <img src="{{ asset('images/logo-TKMutiara.png') }}" alt="Logo TK" class="w-14 h-14 filter brightness-0 invert">
                </div>
                <h3 class="font-bold text-xl mb-2 tracking-tight max-w-sm">Sistem Pembayaran Administrasi TK Mutiara Bogor</h3>
                <p class="text-xs text-blue-100/80 max-w-xs leading-relaxed font-medium">
                    Kelola data administrasi dan keuangan siswa dengan lebih mudah, transparan, dan terintegrasi.
                </p>
            </div>
            
            <div class="mt-auto text-[10px] text-blue-200/50 tracking-wider">
                &copy; 2026 TK Mutiara Bogor
            </div>
        </div>

        <!-- SISI KANAN: FORM INPUT -->
        <div class="w-full md:w-1/2 p-6 md:p-10 flex flex-col justify-center space-y-6">

            <!-- Identitas Atas Form -->
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-[#1E88E5] text-white rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm tracking-tight">TK Mutiara Bogor</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sistem Informasi Pembayaran</p>
                </div>
            </div>

            <div class="space-y-1">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Login Admin</h2>
                <p class="text-xs text-slate-500 font-medium">Silakan masukkan akun Anda untuk mengakses dashboard admin.</p>
            </div>

            <!-- Notifikasi Gagal -->
            @if(session('gagal'))
                <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 animate-shake">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 shrink-0"></i>
                    <span>{{ session('gagal') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="/login" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 tracking-wide">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="username" required placeholder="Masukkan Email"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium text-slate-700">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-700 tracking-wide">Password</label>
                        <a href="#" class="text-xs text-[#1E88E5] hover:underline font-semibold">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium text-slate-700">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="remember"
                        class="w-4 h-4 rounded border-slate-300 text-[#1E88E5] focus:ring-[#1E88E5]/20 focus:ring-offset-0">
                    <label for="remember" class="text-xs text-slate-600 font-medium select-none">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit"
                    class="w-full bg-[#1E88E5] hover:bg-[#1565C0] text-white py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/20 transition-all cursor-pointer mt-2 flex items-center justify-center gap-2 transform active:scale-[0.98]">
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Akun Demo Box -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Akun Demo &rArr; User: <span class="bg-white px-1.5 py-0.5 rounded border border-slate-200 text-blue-600 font-mono font-bold">admin</span> | Pass: <span class="bg-white px-1.5 py-0.5 rounded border border-slate-200 text-blue-600 font-mono font-bold">admin123</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>