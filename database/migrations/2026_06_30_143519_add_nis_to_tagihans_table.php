<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Kita hapus ->after('id') agar aman dimasukkan ke kolom paling akhir
            $table->string('nis'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Bagian ini diisi agar jika di-rollback, kolom nis akan dihapus
            $table->dropColumn('nis');
        });
    }
};