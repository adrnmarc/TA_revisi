<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTagihan extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'kategori_tagihans';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'nama_kategori',
        'harga_default',
        'bisa_dicicil', // 1 untuk bisa, 0 untuk tidak
        'maksimal_angsuran',
    ];

    /**
     * Relasi ke tabel Tagihan (One to Many)
     * Satu kategori bisa dipakai oleh banyak tagihan
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'id_kategori', 'id');
    }
}