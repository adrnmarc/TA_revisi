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
        'id_kategori',
        'id_siswa', 
        'nis',   
        'nama_tagihan',
        'nominal',
        'jatuh_tempo',
        'status',
    ];

    /**
     * Relasi ke master Kategori Tagihan
     */
    public function kategoriTagihan()
    {
        return $this->belongsTo(KategoriTagihan::class, 'id_kategori', 'id');
    }

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    /**
     * Relasi ke detail tagihan
     */
    public function detailTagihan()
    {
        return $this->hasOne(DetailTagihan::class, 'id_tagihan', 'id_tagihan');
    }
}