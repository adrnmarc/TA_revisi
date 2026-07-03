<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\DetailTagihan;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    $query = DetailTagihan::with(['siswa', 'tagihan']);

    if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    $riwayatTransaksi = $query->latest()->get();

    // Perhitungan Statistik
    $totalPendapatan = (clone $query)->where('status_tagihan', 'Lunas')->sum('jumlah_bayar');
    $totalTagihan = \App\Models\DetailTagihan::sum('jumlah_bayar');
    $jumlahMenunggu = (clone $query)->where('status_tagihan', 'Menunggu Verifikasi')->count();
    $belumBayar = $totalTagihan - $totalPendapatan;
    
   
    $siswaLunas = (clone $query)->where('status_tagihan', 'Lunas')->count();
    $siswaBelumLunas = (clone $query)->where('status_tagihan', '!=', 'Lunas')->count();

    return view('admin.laporan', compact(
        'totalPendapatan', 
        'totalTagihan', 
        'jumlahMenunggu', 
        'belumBayar', 
        'riwayatTransaksi',
        'siswaLunas',
        'siswaBelumLunas' 
    ));
}

    public function exportPdf(Request $request)
{
    $query = DetailTagihan::with(['siswa', 'tagihan']);

    if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    $riwayatTransaksi = $query->latest()->get();
    
    $pdf = Pdf::loadView('admin.laporan_pdf', compact('riwayatTransaksi'));
    
    return $pdf->download('laporan-keuangan-' . date('Y-m-d') . '.pdf');
}
}