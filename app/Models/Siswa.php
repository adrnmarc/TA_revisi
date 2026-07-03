<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas'; 

    protected $fillable = [
        'nis', 'nama', 'kelas', 'wali', 'kontak', 'password', 
        'nama_orangtua', 'jenis_kelamin', 'tanggal_lahir', 'alamat'
    ];
    
}