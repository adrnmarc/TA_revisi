<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTagihan extends Model
{
    use HasFactory;

    protected $table = 'detail_tagihans';

    protected $primaryKey = 'id_detail';

    public $incrementing = true;

    protected $fillable = [
        'id_tagihan',
        'id_siswa',
        'nama_iuran',
        'jumlah_bayar',
        'status_tagihan',
        'bukti_bayar',
    ];

    /**
     * Relasi ke Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }

    /**
     * Relasi ke Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    /**
     * Relasi ke Pembayaran (diubah jadi pembayarans agar cocok dengan withSum)
     */
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'id_detail', 'id_detail');
    }

    /**
     * Total pembayaran yang sudah masuk (menggunakan jumlah_diterima)
     */
    public function getTotalDibayarAttribute()
    {
        // Mengambil dari relasi pembayarans
        return $this->pembayarans()->sum('jumlah_diterima');
    }

    /**
     * Sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        return max(0, $this->jumlah_bayar - $this->total_dibayar);
    }
}