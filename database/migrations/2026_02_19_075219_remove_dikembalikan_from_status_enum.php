<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE peminjamans 
            MODIFY status ENUM(
                'pending',
                'disetujui',
                'ditolak',
                'pengembalian',
                'terlambat',
                'selesai'
            ) DEFAULT 'pending'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE peminjamans 
            MODIFY status ENUM(
                'pending',
                'disetujui',
                'ditolak',
                'pengembalian',
                'terlambat',
                'dikembalikan',
                'selesai'
            ) DEFAULT 'pending'
        ");
    }
};

