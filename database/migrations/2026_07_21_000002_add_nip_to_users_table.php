<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 50)->nullable()->unique()->after('name');
        });

        DB::table('pegawai')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->each(function ($pegawai) {
                DB::table('users')->where('id', $pegawai->user_id)->update(['nip' => $pegawai->nip]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn('nip');
        });
    }
};
