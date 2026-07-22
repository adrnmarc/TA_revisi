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

        // Perbaikan logika filter menggunakan relasi tabel pembayarans
        if ($status === 'menunggu') {
            // Cari tagihan yang MEMILIKI pembayaran berstatus 'Menunggu Verifikasi'
            $query->whereHas('pembayarans', function ($q) {
                $q->where('status', 'Menunggu Verifikasi');
            });
        } elseif ($status === 'disetujui') {
            // Cari tagihan yang MEMILIKI pembayaran yang sudah disetujui ('Diterima')
            $query->whereHas('pembayarans', function ($q) {
                $q->where('status', 'Diterima');
            });
        } elseif ($status === 'ditolak') {
            // Cari tagihan yang MEMILIKI pembayaran yang statusnya 'Ditolak'
            $query->whereHas('pembayarans', function ($q) {
                $q->where('status', 'Ditolak');
            });
        }

        // Eager load 'pembayarans' untuk performa yang lebih cepat (menghindari N+1 Query)
        $pembayarans = $query->with(['siswa', 'pembayarans'])->latest()->get();
        
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

        // ====================================================================
        // KUNCI KEAMANAN: Nominal TIDAK diambil dari input form sama sekali.
        // ====================================================================
        $pembayaranPending = Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->first();

        // FIX: Kalau tidak ada pembayaran yang benar-benar menunggu verifikasi,
        // JANGAN proses apa pun. Sebelumnya kode ini fallback ke $sisaTagihanAsli,
        // yang menyebabkan tombol Konfirmasi yang salah ke-klik (padahal orang tua
        // belum mengirim cicilan baru) bikin tagihan langsung ditandai Lunas.
        if (!$pembayaranPending) {
            return back()->with('gagal', 'Tidak ada bukti pembayaran baru yang menunggu verifikasi untuk tagihan ini.');
        }

        $inputNominal = (float) $pembayaranPending->jumlah_diterima;

        // 2. Mencegah pelunasan jika tagihan memang sudah lunas 100% dari awal
        if ($sisaTagihanAsli <= 0) {
            return back()->with('gagal', 'Tagihan ini sebenarnya sudah lunas sepenuhnya.');
        }

        // 3. Cek agar tidak over-payment
        if ($inputNominal > $sisaTagihanAsli) {
            $inputNominal = $sisaTagihanAsli;
        }

        // 4. UPDATE TRANSAKSI PENDING JADI DITERIMA
        $pembayaranPending->update([
            'status'          => 'Diterima',
            'jumlah_diterima' => $inputNominal,
            'user_id'         => Auth::id() ?? 1,
            'tanggal_bayar'   => now()->format('Y-m-d'),
        ]);

        // 5. HITUNG TOTAL KESELURUHAN & UPDATE STATUS TAGIHAN INDUK
        $totalSekarang = $totalDiterimaSaja + $inputNominal;

        $detail->update([
            'status_tagihan' => ($totalSekarang >= $detail->jumlah_bayar) ? 'Lunas' : 'Mencicil',
            'bukti_bayar'    => null, 
        ]);

        return back()->with('sukses', 'Pembayaran sebesar Rp ' . number_format($inputNominal, 0, ',', '.') . ' berhasil diverifikasi!');
    }

    public function tolakVerifikasi(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string']);
        $detail = DetailTagihan::findOrFail($id);

        $adaYangDitolak = Pembayaran::where('id_detail', $detail->id_detail)
            ->where('status', 'Menunggu Verifikasi')
            ->update([
                'status' => 'Ditolak',
                'keterangan' => $request->alasan
            ]);

        // FIX: kalau tidak ada pembayaran pending yang ditolak, jangan ubah status tagihan
        if ($adaYangDitolak === 0) {
            return back()->with('gagal', 'Tidak ada bukti pembayaran baru yang menunggu verifikasi untuk tagihan ini.');
        }

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