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
            if (Schema::hasColumn('peminjamans', 'status')) {
                $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan', 'selesai', 'pending_back'])->default('pending')->change();
            }

            if (!Schema::hasColumn('peminjamans', 'tanggal_pengembalian_selesai')) {
                $table->dateTime('tanggal_pengembalian_selesai')->nullable()->after('tanggal_pengembalian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            //
        });
    }
};
