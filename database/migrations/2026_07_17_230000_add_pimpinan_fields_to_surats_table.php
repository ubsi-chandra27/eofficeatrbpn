<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->foreignId('jabatan_pimpinan_id')
                ->nullable()
                ->after('tujuan_surat')
                ->constrained('jabatan')
                ->nullOnDelete();
            $table->string('nama_pimpinan')->nullable()->after('jabatan_pimpinan_id');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['jabatan_pimpinan_id']);
            $table->dropColumn(['jabatan_pimpinan_id', 'nama_pimpinan']);
        });
    }
};
