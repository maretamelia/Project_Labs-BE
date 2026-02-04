<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // ubah enum, tambah 'pengembalian'
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan', 'pengembalian'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // rollback ke enum lama
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])
                  ->default('pending')
                  ->change();
        });
    }
};
