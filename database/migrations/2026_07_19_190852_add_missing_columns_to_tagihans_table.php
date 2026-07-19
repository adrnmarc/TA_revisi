<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kategori')->nullable()->after('id_tagihan');
            $table->unsignedBigInteger('id_siswa')->nullable()->after('id_kategori');
            $table->decimal('nominal', 15, 2)->default(0)->after('nama_tagihan');
            $table->string('status')->default('Belum Lunas')->after('jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn(['id_kategori', 'id_siswa', 'nominal', 'status']);
        });
    }
};