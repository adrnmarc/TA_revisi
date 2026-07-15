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
        
        // 1. Cari data riwayat pembayaran yang berstatus 'Menunggu Verifikasi' milik tagihan ini
        $pembayaranPending = Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->first();

        // Ambil nominal bayar dari input admin, jika kosong/null gunakan nominal dari pembayaran pending
        $inputNominal = $request->jumlah_diterima ? (float) $request->jumlah_diterima : ($pembayaranPending ? $pembayaranPending->jumlah_diterima : $detail->jumlah_bayar);

        // 2. Hitung total yang SUDAH BERSTATUS 'Diterima' / 'Lunas' (Abaikan yang masih pending)
        $totalDiterimaSaja = Pembayaran::where('id_detail', $detail->id_detail)
            ->whereIn('status', ['Diterima', 'Lunas'])
            ->sum('jumlah_diterima');
        
        $sisaTagihan = $detail->jumlah_bayar - $totalDiterimaSaja;

        // 3. CEK: Nominal tidak boleh 0 atau negatif
        if ($inputNominal <= 0) {
            return back()->with('gagal', 'Nominal tidak valid!');
        }

        // 4. CEK: Nominal tidak boleh melebihi sisa
        if ($inputNominal > $sisaTagihan) {
            return back()->with('gagal', 'Nominal melebihi sisa tagihan! Sisa yang harus dibayar: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
        }

        // 5. UPDATE ATAU BUAT TRANSAKSI BARU
        if ($pembayaranPending) {
            // Jika transaksi dari portal ortu sudah ada, ubah statusnya menjadi 'Diterima' dan update nominalnya jika admin menyesuaikan nilainya
            $pembayaranPending->update([
                'status'          => 'Diterima',
                'jumlah_diterima' => $inputNominal,
                'user_id'         => Auth::id() ?? 1,
                'tanggal_bayar'   => now()->format('Y-m-d'),
            ]);
        } else {
            // Jika tidak ada data pending (pembayaran manual via admin), buat baris baru
            Pembayaran::create([
                'id_detail'       => $detail->id_detail,
                'user_id'         => Auth::id() ?? 1,
                'tanggal_bayar'   => now()->format('Y-m-d'),
                'jumlah_diterima' => $inputNominal,
                'bukti_bayar'     => $detail->bukti_bayar,
                'status'          => 'Diterima'
            ]);
        }

        // 6. HITUNG TOTAL KESELURUHAN & UPDATE STATUS TAGIHAN
        $totalSekarang = $totalDiterimaSaja + $inputNominal;
        
        $detail->update([
            'status_tagihan' => ($totalSekarang >= $detail->jumlah_bayar) ? 'Lunas' : 'Mencicil',
            'bukti_bayar'    => null, // Bersihkan temp bukti bayar karena sudah aman di record pembayaran
        ]);

        return back()->with('sukses', 'Pembayaran sebesar Rp ' . number_format($inputNominal, 0, ',', '.') . ' berhasil diverifikasi!');
    }

    public function tolakVerifikasi(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);
        $detail = DetailTagihan::findOrFail($id);
        
        // Cari data pembayaran pending untuk dihapus atau dibatalkan
        Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->delete();

        if ($detail->bukti_bayar) {
            $fotoTerpakaiDiTempatLain = DetailTagihan::where('bukti_bayar', $detail->bukti_bayar)
                                        ->where('id_detail', '!=', $id)
                                        ->exists();

            if (!$fotoTerpakaiDiTempatLain) {
                Storage::disk('public')->delete($detail->bukti_bayar);
            }
        }

        $detail->update([
            'status_tagihan' => 'Ditolak', 
            'bukti_bayar' => null
        ]);
        
        return back()->with('sukses', 'Pembayaran ditolak: ' . $request->alasan);
    }
}