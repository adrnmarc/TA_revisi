<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TK Mutiara Bogor</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *{
            font-family: 'Poppins', sans-serif;
        }

        html{
            scroll-behavior:smooth;
        }

        body{
            background:#f8fafc;
        }

        .hero{
            background:linear-gradient(rgba(15,23,42,.65),rgba(15,23,42,.65)),
            url('https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=1600');
            background-size:cover;
            background-position:center;
        }

        .menu:hover{
            color:#2563eb;
            transition:.3s;
        }

        .btn{
            transition:.3s;
        }

        .btn:hover{
            transform:translateY(-3px);
        }

    </style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md shadow z-50">

    <div class="max-w-7xl mx-auto px-8">

        <div class="flex justify-between items-center h-20">

            <div class="flex items-center gap-3">

                <img src="{{ asset('images/logo-TKMutiara.png') }}" class="w-16 h-16 object-contain" alt="Logo TK Mutiara Bogor">

                <div>
                    <h1 class="font-bold text-2xl text-blue-700">
                        TK Mutiara Bogor
                    </h1>

                    <p class="text-xs text-slate-500">
                        Sistem Informasi Pembayaran
                    </p>
                </div>

            </div>

            <div class="hidden lg:flex gap-8 font-medium text-slate-700">

                <a href="#beranda" class="menu">Beranda</a>

                <a href="#tentang" class="menu">Tentang</a>

                <a href="#program" class="menu">Program</a>

                <a href="#pengumuman" class="menu">Pengumuman</a>

                <a href="#kontak" class="menu">Kontak</a>

            </div>

            <div class="flex justify-end gap-3">

    <a href="/login"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700
               text-white px-6 py-2.5 rounded-xl font-semibold text-sm
               shadow-sm transition-colors duration-200">

        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zM4 20a8 8 0 1116 0" />
        </svg>

        Login Admin

    </a>

    <a href="/login-ortu"
        class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600
               text-white px-6 py-2.5 rounded-xl font-semibold text-sm
               shadow-sm transition-colors duration-200">

        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-4a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 010 7.75" />
        </svg>

        Login Orang Tua

    </a>

</div>

        </div>

    </div>

</nav>

<!-- ================= HERO ================= -->

<section id="beranda" class="hero min-h-screen flex items-center">

    <div class="max-w-7xl mx-auto px-8 w-full">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>

                <span class="bg-yellow-400 text-slate-900 px-5 py-2 rounded-full font-semibold">

                    🎓 Penerimaan Peserta Didik Baru Tahun Ajaran 2026 / 2027

                </span>

                <h1 class="text-6xl font-extrabold text-white mt-8 leading-tight">

                    Tempat Terbaik

                    <br>

                    Untuk Awal Pendidikan

                    <span class="text-yellow-300">

                        Buah Hati Anda

                    </span>

                </h1>

                <p class="text-slate-200 mt-8 text-lg leading-9">

                    TK Mutiara Bogor merupakan sekolah anak usia dini yang
                    mengembangkan kemampuan akademik, karakter,
                    kreativitas, sosial, dan spiritual anak melalui pembelajaran
                    yang menyenangkan.

                </p>

                <div class="flex gap-5 mt-10">

                    <a href="#program"
                        class="btn bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg">

                        Lihat Program

                    </a>

                    <a href="#kontak"
                        class="btn border-2 border-white text-white px-8 py-4 rounded-xl font-semibold">

                        Hubungi Kami


                    </a>

                </div>

            </div>



            <div>

                <div class="bg-white rounded-3xl shadow-2xl p-8">

                    <h2 class="text-3xl font-bold text-slate-800">

                        Mengapa Memilih Kami?

                    </h2>

                    <div class="space-y-6 mt-8">

                        <div class="flex gap-4">

                            <div class="text-4xl">👩‍🏫</div>

                            <div>

                                <h3 class="font-bold">
                                    Guru Profesional
                                </h3>

                                <p class="text-slate-500">
                                    Seluruh guru berpengalaman dalam pendidikan anak usia dini.
                                </p>

                            </div>

                        </div>

                        <div class="flex gap-4">

                            <div class="text-4xl">🎨</div>

                            <div>

                                <h3 class="font-bold">
                                    Belajar Sambil Bermain
                                </h3>

                                <p class="text-slate-500">
                                    Metode pembelajaran kreatif sehingga anak tidak mudah bosan.
                                </p>

                            </div>

                        </div>

                        <div class="flex gap-4">

                            <div class="text-4xl">🏫</div>

                            <div>

                                <h3 class="font-bold">
                                    Fasilitas Lengkap
                                </h3>

                                <p class="text-slate-500">
                                    Playground, ruang musik, CCTV dan ruang audio visual.
                                </p>

                            </div>

                        </div>

                        <div class="flex gap-4">

                            <div class="text-4xl">❤️</div>

                            <div>

                                <h3 class="font-bold">
                                    Pembentukan Karakter
                                </h3>

                                <p class="text-slate-500">
                                    Anak diajarkan disiplin, mandiri, sopan santun dan nilai agama.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ================= TENTANG ================= -->

<section id="tentang" class="py-24 bg-slate-50">

<div class="max-w-7xl mx-auto px-8">

<div class="grid lg:grid-cols-2 gap-16 items-center">

<div>

<img
src="{{ asset('images/tk-siswa_gambar.jpeg') }}"
class="rounded-3xl shadow-xl w-full h-[550px] object-cover">

</div>

<div>

<span class="text-blue-600 font-semibold uppercase tracking-widest">

Tentang Sekolah

</span>

<h2 class="text-5xl font-bold mt-4 leading-tight">

Menciptakan Anak yang

<span class="text-blue-600">

Cerdas,

</span>

Mandiri dan Berkarakter

</h2>

<p class="mt-8 text-slate-600 leading-9">

TK Mutiara Bogor berkomitmen memberikan pendidikan terbaik
kepada anak usia dini melalui pembelajaran aktif,
bermain sambil belajar,
serta pembentukan karakter berdasarkan nilai moral
dan agama.

</p>

<div class="grid grid-cols-2 gap-6 mt-10">

<div class="bg-white p-6 rounded-2xl shadow">

<h3 class="font-bold text-lg">

🎨 Kreativitas

</h3>

<p class="text-slate-500 mt-2">

Mengembangkan imajinasi anak.

</p>

</div>

<div class="bg-white p-6 rounded-2xl shadow">

<h3 class="font-bold text-lg">

📚 Akademik

</h3>

<p class="text-slate-500 mt-2">

Persiapan menuju Sekolah Dasar.

</p>

</div>

<div class="bg-white p-6 rounded-2xl shadow">

<h3 class="font-bold text-lg">

🤝 Sosial

</h3>

<p class="text-slate-500 mt-2">

Belajar bekerja sama dan berbagi.

</p>

</div>

<div class="bg-white p-6 rounded-2xl shadow">

<h3 class="font-bold text-lg">

❤️ Karakter

</h3>

<p class="text-slate-500 mt-2">

Membentuk anak disiplin dan mandiri.

</p>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- ================= VISI MISI ================= -->

<section class="py-24 bg-white">

<div class="max-w-7xl mx-auto px-8">

<h2 class="text-5xl font-bold text-center">

Visi & Misi

</h2>

<p class="text-center text-slate-500 mt-4">

Komitmen kami dalam membangun generasi masa depan.

</p>

<div class="grid lg:grid-cols-2 gap-10 mt-16">

<div class="bg-blue-600 rounded-3xl p-10 text-white shadow-xl">

<h3 class="text-3xl font-bold mb-6">

Visi

</h3>

<p class="leading-9">

Menjadi sarana menyebarkan ilmu, menggali potensi demi pembentukan generasi yang cerdas , berahlak mulia dan Kreatif

</p>

</div>

<div class="bg-white rounded-3xl p-10 shadow-xl border">

<h3 class="text-3xl font-bold mb-6 text-blue-700">

Misi

</h3>

<ul class="space-y-4 text-slate-600">

<li>✅ Menjadi Satuan Pendidikan  penyebar ilmu     sebagai  rujukan di Kota Bogor</li>

<li>✅ Menciptakan  lingkungan belajar yang aman,nyaman, inklusif dan menyenangkan</li>

<li>✅ Menggali potensi peserta didik menjadi generasi yang beriman, bertaqwa kepada Tuhan Yang Maha Esa, berakhlak Mulia, berkebhinekaan, bergotong royong, Mandiri, bernalar kritis dan kreatif.</li>

</ul>

</div>

</div>

</div>

</section>


<!-- ================= PROGRAM ================= -->

<section id="program" class="py-24 bg-slate-50">

    <div class="max-w-7xl mx-auto px-8">

        <div class="text-center">

            <h2 class="text-5xl font-bold text-slate-800">
                Program Pendidikan
            </h2>

            <p class="text-slate-500 mt-4">
                Program pembelajaran sesuai usia perkembangan anak.
            </p>

        </div>

        <div class="grid md:grid-cols-3 gap-8 mt-16">

            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition">

                <div class="text-5xl mb-5">👶</div>

                <h3 class="text-2xl font-bold">
                    Kelompok A
                </h3>

                <p class="mt-4 text-slate-600 leading-8">
                    Untuk anak usia 4-5 tahun dengan fokus pada motorik,
                    kreativitas, sosialisasi, dan pengenalan huruf serta angka.
                </p>

            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition">

                <div class="text-5xl mb-5">🎒</div>

                <h3 class="text-2xl font-bold">
                    Kelompok B
                </h3>

                <p class="mt-4 text-slate-600 leading-8">
                    Persiapan menuju Sekolah Dasar dengan pembelajaran
                    membaca, menulis, berhitung, serta karakter.
                </p>

            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition">

                <div class="text-5xl mb-5">🎨</div>

                <h3 class="text-2xl font-bold">
                    Ekstrakurikuler
                </h3>

                <p class="mt-4 text-slate-600 leading-8">
                    Menggambar, Menari,
                    Mewarnai,
                    Musik,
                    Tahfidz,
                    serta kegiatan outbound.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- ================= BIAYA ================= -->

<section class="py-24 bg-white">

<div class="max-w-7xl mx-auto px-8">

<div class="text-center">

<h2 class="text-5xl font-bold">

Paket Biaya Pendidikan

</h2>

<p class="text-slate-500 mt-4">

Biaya transparan tanpa biaya tersembunyi.

</p>

</div>

<div class="grid lg:grid-cols-3 gap-10 mt-16">

<div class="rounded-3xl border p-10">

<h3 class="text-2xl font-bold">

Uang Program

</h3>

<div class="text-5xl font-extrabold text-blue-600 mt-6">

Rp1.000.000

</div>

<ul class="mt-8 space-y-3">

<li>✅ Kegiatan Hari Besar</li>

<li>✅ Manasik Haji</li>

<li>✅ Tes IQ</li>

<li>✅ Field Trip</li>

</ul>

</div>

<div class="rounded-3xl border-4 border-blue-600 p-10 shadow-2xl">

<div class="bg-blue-600 text-white inline-block px-5 py-2 rounded-full text-sm">

Paling Populer

</div>

<h3 class="text-2xl font-bold mt-6">

SPP Bulanan

</h3>

<div class="text-5xl font-extrabold text-blue-600 mt-6">

Rp150.000

</div>

<ul class="mt-8 space-y-3">

<li>✅ Pembelajaran</li>

<li>✅ Kelompok Belajar</li>

<li>✅ Kegiatan</li>

<li>✅ Laporan Perkembangan</li>

</ul>

</div>

<div class="rounded-3xl border p-10">

<h3 class="text-2xl font-bold">

Ekstrakurikuler

</h3>

<div class="text-5xl font-extrabold text-blue-600 mt-6">

Rp100.000

</div>

<ul class="mt-8 space-y-3">

<li>✅ Drumband</li>

<li>✅ Mewarnai</li>

<li>✅ Musik</li>

<li>✅ Tahfidz</li>

</ul>

</div>

</div>

</div>

</section>

<!-- ================= GALERI ================= -->

<section class="py-24 bg-slate-50">

<div class="max-w-7xl mx-auto px-8">

<div class="text-center">

<h2 class="text-5xl font-bold">

Galeri Sekolah

</h2>

<p class="text-slate-500 mt-4">

Dokumentasi kegiatan belajar.

</p>

</div>

<div class="grid md:grid-cols-3 gap-8 mt-16">

    <img src="{{ asset('images/tk-projectbelajar.jpeg') }}"
        class="rounded-3xl shadow-xl hover:scale-105 transition duration-300" alt="Dokumentasi Belajar">

    <img src="{{ asset('images/tk-drumband.jpeg') }}"
        class="rounded-3xl shadow-xl hover:scale-105 transition duration-300" alt="Kegiatan Drumband">

    <img src="{{ asset('images/tk-kartini.jpeg') }}"
        class="rounded-3xl shadow-xl hover:scale-105 transition duration-300" alt="Siswa Mewarnai">

</div>

</div>

</section>

<!-- ================= PENGUMUMAN ================= -->

<section id="pengumuman" class="py-24 bg-white">

<div class="max-w-7xl mx-auto px-8">

<div class="text-center">

<h2 class="text-5xl font-bold">

Pengumuman Terbaru

</h2>

<p class="text-slate-500 mt-4">

Informasi terbaru dari TK Mutiara Bogor.

</p>

</div>

<div class="mt-16 space-y-8">

@forelse($pengumuman as $item)

@php
    $badgeKategori = match($item->kategori) {
        'pembayaran' => ['label' => 'Pembayaran', 'kelas' => 'bg-blue-100 text-blue-600'],
        'kegiatan'   => ['label' => 'Kegiatan',   'kelas' => 'bg-emerald-100 text-emerald-600'],
        'libur'      => ['label' => 'Libur',      'kelas' => 'bg-amber-100 text-amber-600'],
        'penting'    => ['label' => 'Penting',    'kelas' => 'bg-rose-100 text-rose-600'],
        default      => ['label' => 'Umum',       'kelas' => 'bg-slate-100 text-slate-500'],
    };
@endphp

<div class="bg-slate-50 rounded-3xl p-8 shadow">

<div class="flex justify-between items-center">

<div class="flex items-center gap-3">

<h3 class="font-bold text-2xl">

{{ $item->judul }}

</h3>

<span class="{{ $badgeKategori['kelas'] }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">

{{ $badgeKategori['label'] }}

</span>

</div>

<span class="bg-blue-100 text-blue-600 px-4 py-2 rounded-full text-sm">

{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}

</span>

</div>

<p class="mt-5 text-slate-600 leading-8">

{{ $item->isi }}

</p>

</div>

@empty

<div class="text-center py-16">

<h3 class="text-2xl font-bold">

Belum Ada Pengumuman

</h3>

<p class="text-slate-400 mt-3">

Silakan cek kembali nanti.

</p>

</div>

@endforelse

</div>

</div>

</section>

<!-- ================= KONTAK ================= -->
<section id="kontak" class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-8">

        <div class="text-center">
            <h2 class="text-5xl font-bold text-slate-800">
                Kontak Kami
            </h2>
            <p class="text-slate-500 mt-4">
                Hubungi kami atau ikuti media sosial kami untuk informasi lebih lanjut.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mt-16">

            <!-- Card WhatsApp -->
            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center justify-between">
                <div class="flex flex-col items-center">
                    <!-- Logo Link WhatsApp (Bisa Diklik Langsung) -->
                    <a href="https://wa.me/6285716698823" target="_blank" 
                       class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-emerald-100/50 hover:scale-110 transition-transform duration-200 cursor-pointer">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.444 5.703 1.445h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    
                    <h3 class="text-2xl font-bold text-slate-800">
                        WhatsApp
                    </h3>
                    <p class="mt-4 text-slate-600 leading-8">
                        Layanan chat cepat untuk informasi pendaftaran, biaya, dan pertanyaan umum seputar sekolah.
                    </p>
                </div>
            </div>

            <!-- Card Instagram -->
            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center justify-between">
                <div class="flex flex-col items-center">
                    <!-- Logo Link Instagram (Bisa Diklik Langsung) -->
                    <a href="https://www.instagram.com/tk_mutiara_bogor?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" 
                       class="w-14 h-14 bg-pink-50 text-pink-500 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-pink-100/50 hover:scale-110 transition-transform duration-200 cursor-pointer">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>

                    <h3 class="text-2xl font-bold text-slate-800">
                        Instagram
                    </h3>
                    <p class="mt-4 text-slate-600 leading-8">
                        Lihat keseruan dokumentasi kegiatan belajar-mengajar dan info pengumuman terbaru kami.
                    </p>
                </div>
            </div>

            <!-- Card Email -->
            <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center justify-between">
                <div class="flex flex-col items-center">
                    <!-- Logo Link Email (Bisa Diklik Langsung) -->
                    <a href="mailto:tkmutiarabogor@ymail.com" 
                       class="w-14 h-14 bg-sky-50 text-sky-500 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-sky-100/50 hover:scale-110 transition-transform duration-200 cursor-pointer">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </a>

                    <h3 class="text-2xl font-bold text-slate-800">
                        Email Resmi
                    </h3>
                    <p class="mt-4 text-slate-600 leading-8">
                        Kirimkan berkas kemitraan, surat administrasi resmi, maupun saran formal ke alamat email kami.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

</section>

<footer class="bg-slate-900 text-white">

<div class="max-w-7xl mx-auto px-6 py-16">

<div class="grid md:grid-cols-4 gap-10">

<div>

<h2 class="text-2xl font-bold text-blue-400">
TK Mutiara Bogor
</h2>

<p class="mt-4 text-slate-400 leading-7">
Mewujudkan generasi yang cerdas,
mandiri, kreatif dan berakhlak mulia.
</p>

</div>

<div>

<h3 class="font-bold mb-5">
Menu
</h3>

<ul class="space-y-3 text-slate-400">

<li><a href="#beranda">Beranda</a></li>

<li><a href="#tentang">Tentang</a></li>

<li><a href="#program">Program</a></li>

<li><a href="#pengumuman">Pengumuman</a></li>

</ul>

</div>

<div>

<h3 class="font-bold mb-5">
Portal

</h3>

<ul class="space-y-3 text-slate-400">

<li><a href="/login">Login Admin</a></li>

<li><a href="/login-ortu">Login Orang Tua</a></li>

</ul>

</div>

<div>

<h3 class="font-bold mb-5">
Jam Operasional
</h3>

<p class="text-slate-400 leading-7">

Senin - Jumat<br>

07.00 - 15.00 WIB

</p>

</div>

</div>

<hr class="border-slate-700 my-10">

<div class="text-center text-slate-500">

© {{ date('Y') }} TK Mutiara Bogor

</div>

</div>

</footer>



<button
id="backToTop"
class="hidden fixed bottom-8 right-8 bg-blue-600 text-white w-14 h-14 rounded-full shadow-xl hover:bg-blue-700 transition">

↑

</button>

<script>

const btn = document.getElementById("backToTop");

window.addEventListener("scroll",()=>{

if(window.scrollY>300){

btn.classList.remove("hidden");

}else{

btn.classList.add("hidden");

}

});

btn.addEventListener("click",()=>{

window.scrollTo({

top:0,

behavior:"smooth"

});

});

const observer = new IntersectionObserver((entries)=>{

entries.forEach(entry=>{

if(entry.isIntersecting){

entry.target.classList.add("opacity-100","translate-y-0");

}

});

});

document.querySelectorAll("section").forEach(sec=>{

sec.classList.add("opacity-0","translate-y-10","transition-all","duration-1000");

observer.observe(sec);

});

</script>