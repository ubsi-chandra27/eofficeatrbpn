<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('kategori_pengajuan', 80)->nullable()->after('jenis_surat');
            $table->string('nomor_kontak', 25)->nullable()->after('asal_surat');
            $table->string('asal_instansi')->nullable()->after('nomor_kontak');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn(['kategori_pengajuan', 'nomor_kontak', 'asal_instansi']);
        });
    }
};
