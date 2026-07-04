<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';
    protected $primaryKey = 'id_tagihan';

    protected $fillable = ['nama_tagihan', 'jatuh_tempo', 'nis'];

    /**
     * Relasi ke DetailTagihan (One to Many)
     */
    public function detailTagihan()
    {
        return $this->hasMany(DetailTagihan::class, 'id_tagihan', 'id_tagihan');
    }
}