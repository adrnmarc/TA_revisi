<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

   
    protected $table = 'pembayarans';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
    'id_detail',
    'user_id',
    'tanggal_bayar',
    'jumlah_diterima',
    'status', 
    'bukti_bayar',
    ];

    /**
     * Relasi balik ke DetailTagihan (Many to One)
     */
    public function detailTagihan()
    {
        return $this->belongsTo(DetailTagihan::class, 'id_detail', 'id_detail');
    }
}