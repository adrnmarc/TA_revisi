<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal TK Mutiara Bogor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center p-6">

    <div class="max-w-5xl w-full text-center space-y-16">
        <div class="space-y-2">
            <h1 class="text-5xl font-bold text-slate-900">TK Mutiara Bogor</h1>
            <p class="text-slate-500 text-lg">Sistem Informasi Pembayaran Administrasi Sekolah</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <a href="/login" class="bg-white p-10 rounded-2xl shadow-xl border-t-8 border-blue-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="bg-blue-50 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-8 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-3">Masuk Sebagai Admin</h2>
                <p class="text-slate-600">Kelola data keuangan, konfirmasi pembayaran, data siswa, dan cetak laporan.</p>
            </a>

            <a href="/login-ortu" class="bg-white p-10 rounded-2xl shadow-xl border-t-8 border-emerald-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="bg-emerald-50 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-8 text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-3">Portal Orang Tua</h2>
                <p class="text-slate-600">Lihat rincian tagihan anak, riwayat pembayaran, profil anak, dan informasi pengumuman.</p>
            </a>

        </div>
    </div>

</body>
</html>