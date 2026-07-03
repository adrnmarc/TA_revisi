<?php

namespace App\Http\Controllers;

use App\Models\DetailTagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $query = DetailTagihan::query();

        if ($status === 'menunggu') {
            $query->where('status_tagihan', 'Menunggu Verifikasi');
        } elseif ($status === 'disetujui') {
            $query->where('status_tagihan', 'Lunas');
        } elseif ($status === 'ditolak') {
            $query->where('status_tagihan', 'Ditolak');
        }

        $pembayarans = $query->with('siswa')->get();
        return view('admin.verifikasi', compact('pembayarans', 'status'));
    }

    public function konfirmasiLunas($id)
    {
        $detail = DetailTagihan::findOrFail($id);
        
        // Update ke Lunas
        $detail->update(['status_tagihan' => 'Lunas']);

        Pembayaran::updateOrCreate(
            ['id_detail' => $detail->id_detail],
            [
                'user_id' => Auth::id() ?? 1,
                'tanggal_bayar' => now()->format('Y-m-d'),
                'jumlah_diterima' => $detail->jumlah_bayar,
                'status' => 'Lunas'
            ]
        );

        return back()->with('sukses', 'Pembayaran berhasil dikonfirmasi!');
    }

    public function tolakVerifikasi(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);
        $detail = DetailTagihan::findOrFail($id);
        
        if ($detail->bukti_bayar) {
            Storage::disk('public')->delete($detail->bukti_bayar);
        }

        $detail->update([
            'status_tagihan' => 'Ditolak', 
            'bukti_bayar' => null, 
        ]);

        return back()->with('sukses', 'Pembayaran ditolak: ' . $request->alasan);
    }
}