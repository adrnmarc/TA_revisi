<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriTagihan;

class KategoriTagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Uang Sekolah (Uang Program)',
                'harga_default' => 1000000,
                'bisa_dicicil' => 1,
                'maksimal_angsuran' => 3,
            ],
            [
                'nama_kategori' => 'Uang Ekskul',
                'harga_default' => 60000,
                'bisa_dicicil' => 0,
                'maksimal_angsuran' => 1,
            ],
            [
                'nama_kategori' => 'Uang POMG',
                'harga_default' => 10000,
                'bisa_dicicil' => 0,
                'maksimal_angsuran' => 1,
            ],
            [
                'nama_kategori' => 'Uang MMP',
                'harga_default' => 50000,
                'bisa_dicicil' => 1,
                'maksimal_angsuran' => 2,
            ],
            [
                'nama_kategori' => 'Uang SPP / Bulan',
                'harga_default' => 150000,
                'bisa_dicicil' => 0,
                'maksimal_angsuran' => 1,
            ],
        ];

        foreach ($kategoris as $kategori) {
            // updateOrCreate agar aman dijalankan berkali-kali (tidak duplikat)
            KategoriTagihan::updateOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                $kategori
            );
        }
    }
}