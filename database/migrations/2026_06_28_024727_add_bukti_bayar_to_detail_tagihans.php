<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() {
    Schema::table('detail_tagihans', function (Blueprint $table) {
        $table->string('bukti_bayar')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_tagihans', function (Blueprint $table) {
            //
        });
    }
};
