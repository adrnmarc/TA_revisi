<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihans', 'kategori_tagihan_id')) {
                $table->foreignId('kategori_tagihan_id')
                      ->nullable()
                      ->after('id_tagihan')
                      ->constrained('kategori_tagihans')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropForeign(['kategori_tagihan_id']);
            $table->dropColumn('kategori_tagihan_id');
        });
    }
};