<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // Tambah 'terlambat' ke enum status
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan', 'pengembalian', 'terlambat'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // Kembalikan enum ke versi sebelumnya (tanpa 'terlambat')
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan', 'pengembalian'])
                  ->default('pending')
                  ->change();
        });
    }
};
