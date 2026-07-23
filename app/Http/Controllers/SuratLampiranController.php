<?php

namespace App\Http\Controllers;

use App\Models\Surat;
class SuratLampiranController extends Controller
{
    public function __invoke(Surat $surat)
    {
        $user = auth()->user();
        $pegawaiId = $user->pegawai?->id;

        $diizinkan = $user->role === 'admin'
            || $surat->user_id === $user->id
            || ($pegawaiId && $surat->disposisiTujuans()->where('pegawai_id', $pegawaiId)->exists());

        abort_unless($diizinkan, 404);
        abort_unless($surat->file_path && $surat->attachmentExists(), 404);

        return $surat->attachmentDisk()
            ->download($surat->file_path, basename($surat->file_path));
    }
}
