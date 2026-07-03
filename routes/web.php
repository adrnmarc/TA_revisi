<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LoginController, OrtuController, SiswaController, 
    TagihanController, LaporanController, PengumumanController, 
    PembayaranController, ProfilAnakController, RiwayatPembayaranController
};
use App\Http\Middleware\CheckAdminLogin;

// --- 1. LANDING PAGE ---
Route::get('/', function () { 
    return view('layouts.landing'); 
});

// --- 2. LOGIN AUTHENTICATION ---
// Login Admin
Route::get('/login', function () { return view('admin.login'); });
Route::post('/login', [LoginController::class, 'loginAdmin']); 
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Login Ortu 
Route::get('/login-ortu', function () { return view('ortu.login'); });
Route::post('/login-ortu', [LoginController::class, 'loginOrtu']); 

// --- 3. RUTE ORANG TUA 
Route::get('/ortu/dashboard', [OrtuController::class, 'dashboard']);
Route::get('/ortu/tagihan', [OrtuController::class, 'tagihan']);
Route::get('/ortu/bayar/{id}', [OrtuController::class, 'formBayar']);
Route::post('/ortu/bayar/{id}', [OrtuController::class, 'prosesBayar']);
Route::get('/riwayat', [RiwayatPembayaranController::class, 'index'])->name('riwayat');
Route::get('/ortu/pengumuman', [OrtuController::class, 'pengumuman'])->name('ortu.pengumuman');
Route::get('/ortu/profil', [ProfilAnakController::class, 'index'])->name('ortu.profil');

// --- 4. RUTE ADMIN (Dilindungi Middleware) ---
Route::middleware([CheckAdminLogin::class])->group(function () {
    
    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        $totalSiswa = \App\Models\Siswa::count();
        $totalPendapatan = \App\Models\DetailTagihan::where('status_tagihan', 'Lunas')->sum('jumlah_bayar');
        $tagihanLunas = \App\Models\DetailTagihan::where('status_tagihan', 'Lunas')->count();
        $menungguVerifikasi = \App\Models\DetailTagihan::where('status_tagihan', 'Menunggu Verifikasi')->count();
        $aktivitasTerbaru = \App\Models\DetailTagihan::with('siswa')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('totalSiswa', 'totalPendapatan', 'tagihanLunas', 'menungguVerifikasi', 'aktivitasTerbaru'));
    });

    // Data Siswa
    Route::get('/admin/siswa', [SiswaController::class, 'index']);
    Route::post('/admin/siswa', [SiswaController::class, 'store']);
    Route::put('/admin/siswa/{id}', [SiswaController::class, 'update']);
    Route::delete('/admin/siswa/{id}', [SiswaController::class, 'destroy']);

    // Kelola Tagihan
    Route::get('/admin/tagihan', [TagihanController::class, 'index']);
    Route::post('/admin/tagihan', [TagihanController::class, 'store']);
    Route::put('/admin/tagihan/{id}', [TagihanController::class, 'update']);
    Route::delete('/admin/tagihan/{id}', [TagihanController::class, 'destroy']);

    // Verifikasi Pembayaran
    Route::post('/admin/pembayaran/konfirmasi/{id}', [PembayaranController::class, 'konfirmasiLunas'])->name('pembayaran.konfirmasi');
    Route::post('/admin/pembayaran/tolak/{id}', [PembayaranController::class, 'tolakVerifikasi'])->name('pembayaran.tolak');
    Route::get('/admin/verifikasi', [PembayaranController::class, 'index'])->name('pembayaran.index');

    // Pengumuman
    Route::get('/admin/pengumuman', [PengumumanController::class, 'index']);
    Route::post('/admin/pengumuman', [PengumumanController::class, 'store']);
    Route::delete('/admin/pengumuman/{id}', [PengumumanController::class, 'destroy']);

    // Laporan Keuangan
    Route::get('/admin/laporan', [LaporanController::class, 'index']);
    Route::get('/admin/laporan/export', [LaporanController::class, 'exportPdf']);
});