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
    Schema::create('detail_tagihans', function (Blueprint $table) {
        $table->id('id_detail');
        $table->unsignedBigInteger('id_tagihan');
        $table->unsignedBigInteger('id_siswa');
        $table->string('nama_iuran');
        $table->integer('jumlah_bayar');
        $table->string('status_tagihan')->default('Belum Lunas');
        $table->timestamps();
        $table->foreign('id_tagihan')->references('id_tagihan')->on('tagihans')->onDelete('cascade');
        $table->foreign('id_siswa')->references('id')->on('siswas')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tagihans');
    }
};
