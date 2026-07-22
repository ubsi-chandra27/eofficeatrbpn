<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $duplicateUserIds = DB::table('pegawai')
                ->whereNotNull('user_id')
                ->select('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('user_id');

            foreach ($duplicateUserIds as $userId) {
                $profiles = DB::table('pegawai')
                    ->where('user_id', $userId)
                    ->get()
                    ->map(function ($profile) {
                        $profile->jumlah_disposisi = DB::table('disposisi_tujuans')
                            ->where('pegawai_id', $profile->id)
                            ->count();

                        return $profile;
                    })
                    ->sortByDesc('jumlah_disposisi')
                    ->values();

                $primary = $profiles->first();

                foreach ($profiles->slice(1) as $duplicate) {
                    DB::table('disposisi_tujuans')
                        ->where('pegawai_id', $duplicate->id)
                        ->update(['pegawai_id' => $primary->id]);

                    DB::table('pegawai')->where('id', $duplicate->id)->delete();
                }
            }
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->unique('user_id', 'pegawai_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropUnique('pegawai_user_id_unique');
        });
    }
};
