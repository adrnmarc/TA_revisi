<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('tagihans', function (Blueprint $table) {
        $table->id('id_tagihan'); // Primary Key
        $table->string('nama_tagihan'); // Contoh: "Tagihan Semester Ganjil 2026"
        $table->date('jatuh_tempo');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
