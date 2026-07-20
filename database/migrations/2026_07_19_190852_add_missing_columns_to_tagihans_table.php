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
            if (!Schema::hasColumn('tagihans', 'id_kategori')) {
                $table->unsignedBigInteger('id_kategori')->nullable()->after('id_tagihan');
            }
            if (!Schema::hasColumn('tagihans', 'id_siswa')) {
                $table->unsignedBigInteger('id_siswa')->nullable()->after('id_kategori');
            }
            if (!Schema::hasColumn('tagihans', 'nominal')) {
                $table->decimal('nominal', 15, 2)->default(0)->after('nama_tagihan');
            }
            if (!Schema::hasColumn('tagihans', 'status')) {
                $table->string('status')->default('Belum Lunas')->after('jatuh_tempo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('tagihans', 'id_kategori')) $columnsToDrop[] = 'id_kategori';
            if (Schema::hasColumn('tagihans', 'id_siswa')) $columnsToDrop[] = 'id_siswa';
            if (Schema::hasColumn('tagihans', 'nominal')) $columnsToDrop[] = 'nominal';
            if (Schema::hasColumn('tagihans', 'status')) $columnsToDrop[] = 'status';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};