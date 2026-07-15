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

                <div class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold">
                    TK
                </div>

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
                                    Playground, ruang kelas nyaman, CCTV dan ruang multimedia.
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

<!-- ================= STATISTIK ================= -->

<section class="py-20 bg-white">

    <div class="max-w-7xl mx-auto px-8">

        <div class="grid md:grid-cols-4 gap-8">

            <div class="bg-blue-50 rounded-3xl p-8 text-center shadow-sm hover:shadow-lg transition">

                <div class="text-5xl mb-4">👦</div>

                <h2 class="text-5xl font-bold text-blue-600">
                    120+
                </h2>

                <p class="mt-3 text-slate-600 font-medium">
                    Siswa Aktif
                </p>

            </div>

            <div class="bg-emerald-50 rounded-3xl p-8 text-center shadow-sm hover:shadow-lg transition">

                <div class="text-5xl mb-4">👩‍🏫</div>

                <h2 class="text-5xl font-bold text-emerald-600">
                    20
                </h2>

                <p class="mt-3 text-slate-600 font-medium">
                    Guru Profesional
                </p>

            </div>

            <div class="bg-orange-50 rounded-3xl p-8 text-center shadow-sm hover:shadow-lg transition">

                <div class="text-5xl mb-4">🏫</div>

                <h2 class="text-5xl font-bold text-orange-500">
                    12
                </h2>

                <p class="mt-3 text-slate-600 font-medium">
                    Tahun Berdiri
                </p>

            </div>

            <div class="bg-pink-50 rounded-3xl p-8 text-center shadow-sm hover:shadow-lg transition">

                <div class="text-5xl mb-4">⭐</div>

                <h2 class="text-5xl font-bold text-pink-500">
                    98%
                </h2>

                <p class="mt-3 text-slate-600 font-medium">
                    Kepuasan Orang Tua
                </p>

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
src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=1200"
class="rounded-3xl shadow-2xl">

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

Menjadi lembaga pendidikan anak usia dini
yang unggul dalam membentuk generasi
yang cerdas, kreatif,
berakhlak mulia,
mandiri,
dan berwawasan global.

</p>

</div>

<div class="bg-white rounded-3xl p-10 shadow-xl border">

<h3 class="text-3xl font-bold mb-6 text-blue-700">

Misi

</h3>

<ul class="space-y-4 text-slate-600">

<li>✅ Mengembangkan kemampuan akademik anak.</li>

<li>✅ Membentuk karakter positif.</li>

<li>✅ Menanamkan nilai agama sejak dini.</li>

<li>✅ Mengembangkan kreativitas anak.</li>

<li>✅ Menjalin komunikasi aktif dengan orang tua.</li>

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

Uang Pangkal

</h3>

<div class="text-5xl font-extrabold text-blue-600 mt-6">

Rp1.000.000

</div>

<ul class="mt-8 space-y-3">

<li>✅ Seragam</li>

<li>✅ Buku Paket</li>

<li>✅ Tas Sekolah</li>

<li>✅ ATK</li>

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

Rp350.000

</div>

<ul class="mt-8 space-y-3">

<li>✅ Pembelajaran</li>

<li>✅ Snack</li>

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

<li>✅ Tari</li>

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

<img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=900"
class="rounded-3xl shadow-xl hover:scale-105 transition duration-300">

<img src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=900"
class="rounded-3xl shadow-xl hover:scale-105 transition duration-300">

<img src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=900"
class="rounded-3xl shadow-xl hover:scale-105 transition duration-300">

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

<div class="bg-slate-50 rounded-3xl p-8 shadow">

<div class="flex justify-between items-center">

<h3 class="font-bold text-2xl">

{{ $item->judul }}

</h3>

<span class="bg-blue-100 text-blue-600 px-4 py-2 rounded-full text-sm">

{{ $item->created_at->format('d M Y') }}

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

{{-- ================= TESTIMONI ================= --}}
<section class="py-24 bg-slate-50">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">

            <h2 class="text-4xl font-bold text-slate-800">
                Apa Kata Orang Tua?
            </h2>

            <p class="text-slate-500 mt-3">
                Kepuasan orang tua adalah motivasi kami untuk terus berkembang.
            </p>

        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-3 transition duration-300">
                <div class="text-yellow-400 text-2xl">★★★★★</div>

                <p class="mt-6 text-slate-600 leading-7 italic">
                    "Guru sangat sabar membimbing anak. Perkembangan anak saya sangat pesat sejak masuk TK Mutiara."
                </p>

                <div class="mt-8">
                    <h4 class="font-bold">Ibu Rina</h4>
                    <p class="text-sm text-slate-400">
                        Orang Tua Murid
                    </p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-3 transition duration-300">
                <div class="text-yellow-400 text-2xl">★★★★★</div>

                <p class="mt-6 text-slate-600 leading-7 italic">
                    "Sistem pembayaran online sangat membantu. Semua transaksi menjadi lebih mudah dipantau."
                </p>

                <div class="mt-8">
                    <h4 class="font-bold">Bapak Andi</h4>
                    <p class="text-sm text-slate-400">
                        Orang Tua Murid
                    </p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-3 transition duration-300">
                <div class="text-yellow-400 text-2xl">★★★★★</div>

                <p class="mt-6 text-slate-600 leading-7 italic">
                    "Lingkungan sekolah sangat bersih, aman, dan nyaman untuk anak-anak belajar."
                </p>

                <div class="mt-8">
                    <h4 class="font-bold">Ibu Sinta</h4>
                    <p class="text-sm text-slate-400">
                        Orang Tua Murid
                    </p>
                </div>
            </div>

        </div>

    </div>

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