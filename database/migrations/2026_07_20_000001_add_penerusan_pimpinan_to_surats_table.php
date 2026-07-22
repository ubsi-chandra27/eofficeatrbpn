<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // String dipakai agar alur dapat berkembang tanpa migrasi enum berulang.
            $table->string('status', 40)->default('draft')->change();
            $table->foreignId('diteruskan_oleh')->nullable()
                ->after('nama_pimpinan')->constrained('users')->nullOnDelete();
            $table->timestamp('diteruskan_pada')->nullable()->after('diteruskan_oleh');
            $table->string('metode_penerusan', 30)->nullable()->after('diteruskan_pada');
            $table->text('catatan_pengantar')->nullable()->after('metode_penerusan');
        });

        DB::table('surats')->where('status', 'menunggu')->update(['status' => 'diajukan']);
        DB::table('surats')->where('status', 'disetujui')->update(['status' => 'diverifikasi']);
        DB::table('surats')->where('status', 'ditolak')->update(['status' => 'dikembalikan']);
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['diteruskan_oleh']);
            $table->dropColumn([
                'diteruskan_oleh',
                'diteruskan_pada',
                'metode_penerusan',
                'catatan_pengantar',
            ]);
        });
    }
};
