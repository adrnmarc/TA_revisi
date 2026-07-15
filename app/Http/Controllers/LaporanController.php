<?php

namespace App\Http\Controllers;

use App\Models\DetailTagihan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan whereHas untuk mengakses kolom 'jatuh_tempo' di tabel tagihan
        $query = DetailTagihan::with(['siswa', 'tagihan']);

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->whereBetween('jatuh_tempo', [$request->start_date, $request->end_date]);
            });
        }

        $riwayatTransaksi = $query->latest()->get();

        // Statistik menggunakan query yang sama agar sinkron
        $totalPendapatan = (clone $query)->where('status_tagihan', 'Lunas')->sum('jumlah_bayar');
        
        // Total Tagihan (Keseluruhan tanpa filter agar relevan)
        $totalTagihan = DetailTagihan::sum('jumlah_bayar');
        
        $siswaLunas = (clone $query)->where('status_tagihan', 'Lunas')->count();
        $siswaBelumLunas = (clone $query)->where('status_tagihan', '!=', 'Lunas')->count();

        return view('admin.laporan', compact(
            'totalPendapatan', 
            'totalTagihan', 
            'riwayatTransaksi',
            'siswaLunas',
            'siswaBelumLunas'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = DetailTagihan::with(['siswa', 'tagihan']);

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->whereBetween('jatuh_tempo', [$request->start_date, $request->end_date]);
            });
        }

        $riwayatTransaksi = $query->latest()->get();
        
        $pdf = Pdf::loadView('admin.laporan_pdf', compact('riwayatTransaksi'));
        
        return $pdf->download('laporan-keuangan-' . date('Y-m-d') . '.pdf');
    }
}