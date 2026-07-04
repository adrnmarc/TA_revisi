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
        'bukti_bayar',
        'status_tagihan',
        'created_at',
    ];

    /**
     * Relasi ke Pembayaran (One to Many)
     * Menghubungkan detail tagihan dengan riwayat cicilan di tabel pembayarans
     */
    public function pembayaran()
    {
        // Parameter ke-2: foreign key di tabel pembayarans ('id_detail')
        // Parameter ke-3: local key di tabel detail_tagihans ('id_detail')
        return $this->hasMany(Pembayaran::class, 'id_detail', 'id_detail');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }
}