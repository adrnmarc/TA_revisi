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

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }
}