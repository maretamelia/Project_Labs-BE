<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();

            // 🔗 RELASI KE CATEGORY
            $table->foreignId('category_id')
                  ->constrained('category_barangs')
                  ->cascadeOnDelete();

            $table->string('nama_barang', 100);
            $table->string('kode_barang'); // boleh varchar
            $table->integer('stok');

            $table->string('image')->nullable(); // path gambar

            $table->text('deskripsi')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
