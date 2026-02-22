<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement("
        ALTER TABLE peminjamans
        MODIFY status ENUM(
            'pending',
            'disetujui',
            'ditolak',
            'dikembalikan',
            'selesai',
            'pending_back',
            'terlambat'
        ) NOT NULL DEFAULT 'pending'
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
            'dikembalikan',
            'selesai',
            'pending_back'
        ) NOT NULL DEFAULT 'pending'
    ");
}
};
