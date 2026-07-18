<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User di-import
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menggunakan updateOrCreate supaya jika dijalankan berkali-kali tidak bikin data double
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kolom untuk ngecek apakah admin sudah ada
            [
                'name' => 'Admin TK Mutiara',
                'password' => Hash::make('admin123'), // Password otomatis di-encrypt aman
                // 'username' => 'admin', // Buka baris ini jika tabel users kamu punya kolom username
            ]
        );
    }
}