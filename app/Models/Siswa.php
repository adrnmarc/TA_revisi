<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tagihan;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
    'nis',
    'nama',
    'nama_panggilan', // Tambahkan ini agar bisa disimpan lewat Laravel
    'kelas',
    'wali',
    'kontak',
    'nama_orangtua',
    'jenis_kelamin',
    'tanggal_lahir',
    'alamat',
    'password',
    ];

    /**
     * Relasi ke Tagihan (One to Many)
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'nis', 'nis');
    }
}