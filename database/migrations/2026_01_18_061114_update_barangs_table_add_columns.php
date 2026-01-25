<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {

            // 🔹 Tambahkan kolom category_id jika belum ada
            if (!Schema::hasColumn('barangs', 'category_id')) {
                $table->foreignId('category_id')
                      ->constrained('category_barangs') // pastikan tabel category_barangs sudah ada
                      ->cascadeOnDelete();
            }

            // 🔹 Tambahkan kolom lain jika belum ada
            if (!Schema::hasColumn('barangs', 'nama_barang')) {
                $table->string('nama_barang', 100);
            }

            if (!Schema::hasColumn('barangs', 'kode_barang')) {
                $table->string('kode_barang');
            }

            if (!Schema::hasColumn('barangs', 'stok')) {
                $table->integer('stok')->default(0);
            }

            if (!Schema::hasColumn('barangs', 'image')) {
                $table->string('image')->nullable();
            }

            if (!Schema::hasColumn('barangs', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }

            if (!Schema::hasColumn('barangs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {

            // 🔹 Drop kolom jika ada
            $cols = [
                'category_id',
                'nama_barang',
                'kode_barang',
                'stok',
                'image',
                'deskripsi',
                'deleted_at'
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('barangs', $col)) {
                    // drop foreign key dulu jika ada
                    if ($col === 'category_id') {
                        $table->dropForeign(['category_id']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
