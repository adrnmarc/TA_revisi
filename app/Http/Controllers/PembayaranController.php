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
        
        // 1. Hitung uang yang BENAR-BENAR sudah masuk & disahkan (Abaikan yang pending/ditolak)
        $totalDiterimaSaja = Pembayaran::where('id_detail', $detail->id_detail)
            ->whereIn('status', ['Diterima', 'Lunas'])
            ->sum('jumlah_diterima');
        
        $sisaTagihanAsli = $detail->jumlah_bayar - $totalDiterimaSaja;

        // 2. Ambil nominal. Kalau form mengirim angka 0 (karena bug data lama), PAKSA sesuai sisa tagihannya.
        $inputNominal = (float) $request->jumlah_diterima;
        
        if ($inputNominal <= 0) {
            $pembayaranPending = Pembayaran::where('id_detail', $detail->id_detail)
                ->where('status', 'Menunggu Verifikasi')
                ->first();
                
            // === INI KUNCI PERBAIKANNYA (BYPASS DATA CACAT) ===
            $inputNominal = ($pembayaranPending && $pembayaranPending->jumlah_diterima > 0) 
                            ? (float) $pembayaranPending->jumlah_diterima 
                            : $sisaTagihanAsli; 
        }

        // 3. Mencegah pelunasan jika tagihan memang sudah lunas 100% dari awal
        if ($sisaTagihanAsli <= 0) {
            return back()->with('gagal', 'Tagihan ini sebenarnya sudah lunas sepenuhnya.');
        }

        // 4. Cek agar tidak over-payment
        if ($inputNominal > $sisaTagihanAsli) {
            return back()->with('gagal', 'Nominal melebihi sisa tagihan! Sisa tagihan sebenarnya: Rp ' . number_format($sisaTagihanAsli, 0, ',', '.'));
        }

        // 5. UPDATE ATAU BUAT TRANSAKSI BARU YANG BERSIH
        $pembayaranPending = Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->first();

        if ($pembayaranPending) {
            $pembayaranPending->update([
                'status'          => 'Diterima',
                'jumlah_diterima' => $inputNominal, // Nominal yang sudah dikoreksi otomatis
                'user_id'         => Auth::id() ?? 1,
                'tanggal_bayar'   => now()->format('Y-m-d'),
            ]);
        } else {
            Pembayaran::create([
                'id_detail'       => $detail->id_detail,
                'user_id'         => Auth::id() ?? 1,
                'tanggal_bayar'   => now()->format('Y-m-d'),
                'jumlah_diterima' => $inputNominal,
                'bukti_bayar'     => $detail->bukti_bayar,
                'status'          => 'Diterima'
            ]);
        }

        // 6. HITUNG TOTAL KESELURUHAN & UPDATE STATUS TAGIHAN INDUK
        $totalSekarang = $totalDiterimaSaja + $inputNominal;
        
        $detail->update([
            'status_tagihan' => ($totalSekarang >= $detail->jumlah_bayar) ? 'Lunas' : 'Mencicil',
            'bukti_bayar'    => null, // Bersihkan cache bukti bayar
        ]);

        return back()->with('sukses', 'Pembayaran sebesar Rp ' . number_format($inputNominal, 0, ',', '.') . ' berhasil diverifikasi!');
    }

    public function tolakVerifikasi(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);
        $detail = DetailTagihan::findOrFail($id);
        
        // Simpan alasan penolakan ke riwayat tanpa menghapus datanya
        Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->update([
                'status' => 'Ditolak',
                'keterangan' => $request->alasan 
            ]);

        // Hapus fisik foto di folder public agar memori server tidak penuh
        if ($detail->bukti_bayar) {
            $fotoTerpakaiDiTempatLain = DetailTagihan::where('bukti_bayar', $detail->bukti_bayar)
                                        ->where('id_detail', '!=', $id)
                                        ->exists();

            if (!$fotoTerpakaiDiTempatLain) {
                $filePath = public_path($detail->bukti_bayar);
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath); 
                }
            }
        }

        $detail->update([
            'status_tagihan' => 'Ditolak', 
            'bukti_bayar' => null
        ]);
        
        return back()->with('sukses', 'Pembayaran ditolak. Alasan berhasil dikirim ke Orang Tua.');
    }
}