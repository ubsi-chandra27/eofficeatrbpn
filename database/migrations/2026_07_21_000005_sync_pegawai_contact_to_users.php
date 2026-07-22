<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('pegawai')->whereNotNull('user_id')->orderBy('id')->each(function ($pegawai) {
            DB::table('users')->where('id', $pegawai->user_id)->update([
                'phone' => $pegawai->no_hp,
                'address' => $pegawai->alamat,
            ]);
        });
    }
    public function down(): void {}
};
