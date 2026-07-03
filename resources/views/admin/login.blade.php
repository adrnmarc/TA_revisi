<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - TK Mutiara Bogor</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#EEF4FA] min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="bg-white rounded-[32px] shadow-xl max-w-4xl w-full overflow-hidden flex flex-col md:flex-row p-4 min-h-[500px] gap-4">
        
        <div class="w-full md:w-1/2 bg-gradient-to-br from-[#1E88E5] to-[#1565C0] rounded-[24px] p-8 flex flex-col items-center justify-center text-center relative overflow-hidden min-h-[250px] md:min-h-auto text-white">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-black/5 rounded-full blur-xl"></div>
            
            <div class="p-4 bg-white/10 rounded-2xl mb-4 backdrop-blur-xs">
                <i data-lucide="school" class="w-12 h-12 text-white"></i>
            </div>
            
            <h3 class="font-bold text-xl mb-2 tracking-tight">Sistem Pembayaran Administrasi TK Mutiara Bogor</h3>
            <p class="text-xs text-blue-100 max-w-xs leading-relaxed font-medium">
                Kelola data administrasi dan keuangan siswa TK Mutiara Bogor dengan lebih mudah, transparan, dan terintegrasi.
            </p>
        </div>

        <div class="w-full md:w-1/2 p-6 md:p-10 flex flex-col justify-center space-y-6">
            
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-[#1E88E5] text-white rounded-xl shadow-md shadow-blue-100">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm tracking-tight">TK Mutiara Bogor</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sistem Informasi Pembayaran</p>
                </div>
            </div>

            <div class="space-y-1">
                <h2 class="text-base font-bold text-slate-800">Login Admin</h2>
                <p class="text-xs text-slate-400 font-medium">Silakan masukkan akun Anda untuk mengakses dashboard admin.</p>
            </div>

            @if(session('gagal'))
                <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 shrink-0"></i>
                    <span>{{ session('gagal') }}</span>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" 
                               name="username" 
                               required 
                               placeholder="admin" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all font-medium text-slate-700">
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-500">Password</label>
                        <a href="#" class="text-xs text-[#1E88E5] hover:underline font-semibold">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#1E88E5] focus:bg-white transition-all font-medium text-slate-700">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="remember" class="w-4 h-4 rounded border-slate-300 text-[#1E88E5] focus:ring-[#1E88E5]">
                    <label for="remember" class="text-xs text-slate-500 font-medium select-none">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="w-full bg-[#0064B0] hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition-all cursor-pointer mt-2 shadow-sm shadow-blue-100 flex items-center justify-center gap-2">
                    <span>Masuk</span>
                </button>
            </form>

            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                <p class="text-[11px] text-slate-500 font-medium">Akun Demo &rArr; User: <span class="font-bold text-slate-800">admin</span> | Pass: <span class="font-bold text-slate-800">admin123</span></p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>