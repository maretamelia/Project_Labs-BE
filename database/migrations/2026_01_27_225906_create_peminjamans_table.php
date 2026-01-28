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
    Schema::create('peminjamans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('barang_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->unsignedInteger('jumlah');

    $table->date('tanggal_pinjam');
    $table->date('tanggal_kembali')->nullable();

    $table->enum('status', [
        'menunggu',
        'disetujui',
        'dipinjam',
        'dikembalikan',
        'ditolak',
        'terlambat'
    ])->default('menunggu');

    $table->text('keterangan')->nullable();

    $table->timestamps();
});
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};