<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Menambahkan kolom nominal dan status ke tabel tagihans yang ada di phpMyAdmin Anda
            $table->integer('nominal')->default(0)->after('nama_tagihan');
            $table->string('status')->default('Belum Lunas')->after('jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn(['nominal', 'status']);
        });
    }
};