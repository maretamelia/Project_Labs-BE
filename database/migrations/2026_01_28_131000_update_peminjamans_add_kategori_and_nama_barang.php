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
        Schema::table('peminjamans', function (Blueprint $table) {
            // tambah kolom kategori untuk relasi ke kategori_barangs
            $table->string('kategori')->after('user_id')->nullable();

            // tambah kolom nama_barang untuk input manual
            $table->string('nama_barang')->after('kategori')->nullable();

            // hapus kolom barang_id karena input manual
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // restore barang_id jika rollback
            $table->foreignId('barang_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->dropColumn('kategori');
            $table->dropColumn('nama_barang');
        });
    }
};
