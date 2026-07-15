<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';
    protected $primaryKey = 'id_tagihan';
    public $incrementing = true;

    protected $fillable = [
        'kategori_tagihan_id', // Menampung relasi kategori iuran
        'nama_tagihan',
        'nominal',
        'jatuh_tempo',
        'status',
        'nis',
    ];

    /**
     * Relasi ke master Kategori Tagihan
     */
    public function kategoriTagihan()
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_tagihan_id', 'id');
    }

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    /**
     * Relasi ke detail tagihan
     */
    public function detailTagihan()
    {
        return $this->hasOne(DetailTagihan::class, 'id_tagihan', 'id_tagihan');
    }
}