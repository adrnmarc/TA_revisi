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

   public function konfirmasiLunas(Request $request, $id)
{
    $detail = DetailTagihan::findOrFail($id);
    $inputNominal = (float) $request->jumlah_diterima;

    // 1. Hitung sisa tagihan yang SEBENARNYA
    $totalSudahBayar = Pembayaran::where('id_detail', $detail->id_detail)->sum('jumlah_diterima');
    $sisaTagihan = $detail->jumlah_bayar - $totalSudahBayar;

    // 2. CEK: Nominal tidak boleh 0 atau negatif
    if ($inputNominal <= 0) {
        return back()->with('gagal', 'Nominal tidak valid!');
    }

    // 3. CEK: Nominal tidak boleh melebihi sisa
    if ($inputNominal > $sisaTagihan) {
        return back()->with('gagal', 'Nominal melebihi sisa tagihan! Sisa yang harus dibayar: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
    }

    // 4. CEK: Batas 3 kali cicilan (Hanya cek jika belum lunas)
    $jumlahTransaksi = Pembayaran::where('id_detail', $detail->id_detail)->count();
    if ($jumlahTransaksi >= 3 && ($sisaTagihan - $inputNominal) > 0) {
        return back()->with('gagal', 'Pembayaran sudah mencapai batas maksimal 3 kali!');
    }

    // 5. Simpan Data Pembayaran
$pembayaran = Pembayaran::create([
    'id_detail'       => $detail->id_detail,
    'user_id'         => Auth::id() ?? 1,
    'tanggal_bayar'   => now()->format('Y-m-d'),
    'jumlah_diterima' => $inputNominal,
    'bukti_bayar'     => $detail->bukti_bayar, // PINDAHKAN FOTO KE SINI
    'status'          => 'Diterima'
]);

// 6. Update Status Tagihan & Kosongkan foto di DetailTagihan
$totalSekarang = $totalSudahBayar + $inputNominal;
$detail->update([
    'status_tagihan' => ($totalSekarang >= $detail->jumlah_bayar) ? 'Lunas' : 'Mencicil',
    'bukti_bayar'    => null, // Hapus referensi foto di sini setelah dipindah ke tabel pembayaran
]);

    return back()->with('sukses', 'Pembayaran sebesar Rp ' . number_format($inputNominal, 0, ',', '.') . ' berhasil diverifikasi!');
}
    public function tolakVerifikasi(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);
        $detail = DetailTagihan::findOrFail($id);
        
        if ($detail->bukti_bayar) {
            // CEK: Apakah foto bukti ini juga terpasang di tagihan lain?
            $fotoTerpakaiDiTempatLain = DetailTagihan::where('bukti_bayar', $detail->bukti_bayar)
                                        ->where('id_detail', '!=', $id)
                                        ->exists();

            // Jika tidak ada tagihan lain yang pakai foto ini, baru hapus dari server
            if (!$fotoTerpakaiDiTempatLain) {
                Storage::disk('public')->delete($detail->bukti_bayar);
            }
        }

        // Update status tagihan ini menjadi ditolak dan kosongkan path fotonya
        $detail->update([
            'status_tagihan' => 'Ditolak', 
            'bukti_bayar' => null
        ]);
        
        return back()->with('sukses', 'Pembayaran ditolak: ' . $request->alasan);
    }
}