<?php

namespace App\Http\Controllers\Pegawai;


use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\LogAktivitas;
use App\Services\PegawaiStarterDataService;
use Illuminate\Support\Facades\Auth;



class DashboardController extends Controller
{

    public function index()
    {

        $user = Auth::user();



        /*
        |--------------------------------------------------------------------------
        | Ambil data pegawai login
        |--------------------------------------------------------------------------
        */


        $pegawai = Pegawai::where(
            'user_id',
            $user->id
        )->first();



        if(!$pegawai){

            abort(
                403,
                'Data pegawai belum terhubung dengan akun login.'
            );

        }

        $starterData = app(PegawaiStarterDataService::class);
        if ($starterData->needsStarterData($pegawai)) {
            $starterData->ensureForPegawai($pegawai);
        }



        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */


        // surat masuk dari disposisi (total tugas disposisi)
        $suratMasuk = Surat::whereHas(
            'disposisiTujuans',
            function($q) use($pegawai){

                $q->where(
                    'pegawai_id',
                    $pegawai->id
                );

            }
        )
        ->count();



        // disposisi yang belum selesai (tugas aktif pegawai)
        $disposisiAktif = DisposisiTujuan::where(
            'pegawai_id',
            $pegawai->id
        )
        ->whereIn('status', ['Belum Dibaca', 'Sudah Dibaca'])
        ->count();



        // surat keluar pegawai
        $suratKeluar = Surat::where(
            'user_id',
            $user->id
        )
        ->where(
            'jenis_surat',
            'keluar'
        )
        ->count();



        // jumlah disposisi
        $disposisi = DisposisiTujuan::where(
            'pegawai_id',
            $pegawai->id
        )
        ->count();



        $statusGroups = [
            'diajukan' => ['menunggu', 'diajukan'],
            'diproses' => ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'],
            'perbaikan' => ['dikembalikan', 'ditolak'],
            'selesai' => ['selesai', 'terkirim', 'diarsipkan'],
        ];

        // surat keluar yang sedang menunggu verifikasi Admin
        $menunggu = Surat::where(
            'user_id',
            $user->id
        )
        ->where('jenis_surat', 'keluar')
        ->whereIn('status', $statusGroups['diajukan'])
        ->count();




        /*
        |--------------------------------------------------------------------------
        | Tugas hari ini
        |--------------------------------------------------------------------------
        */


        $disposisiBelum = DisposisiTujuan::where(
            'pegawai_id',
            $pegawai->id
        )
        ->where(
            'status',
            'Belum Dibaca'
        )
        ->count();




        $prioritasTinggi = DisposisiTujuan::where('pegawai_id', $pegawai->id)
            ->whereIn('status', ['Belum Dibaca', 'Sudah Dibaca'])
            ->whereHas('disposisi', function ($query) {
                $query->where('prioritas', 'Tinggi');
            })
            ->count();




        /*
        |--------------------------------------------------------------------------
        | Surat terbaru
        |--------------------------------------------------------------------------
        */


        $suratTerbaru = Surat::where(
            'user_id',
            $user->id
        )
        ->where('jenis_surat', 'masuk')
        ->latest('updated_at')
        ->take(5)
        ->get();




        /*
        |--------------------------------------------------------------------------
        | Disposisi terbaru
        |--------------------------------------------------------------------------
        */


        $disposisiTerbaru = DisposisiTujuan::with([
            'disposisi.surat',
            'disposisi.pengirim',
        ])
            ->where('pegawai_id', $pegawai->id)
            ->latest('updated_at')
            ->take(5)
            ->get();




        /*
        |--------------------------------------------------------------------------
        | Aktivitas
        |--------------------------------------------------------------------------
        */


        $aktivitasTerbaru = LogAktivitas::with('surat')
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get()
            ->map(function (LogAktivitas $log) use ($user) {
                $surat = $log->surat;

                return [
                    'jam' => $log->created_at,
                    'jenis' => $log->action,
                    'nomor' => $surat?->nomor_surat ?? '-',
                    'keterangan' => $log->description,
                    'status' => $surat?->status,
                    'url' => $surat && $surat->user_id === $user->id
                        ? ($surat->jenis_surat === 'keluar'
                            ? route('pegawai.surat-keluar.show', $surat->id)
                            : route('pegawai.surat-masuk.show', $surat->id))
                        : null,
                ];
            });



        return view(
            'pegawai.dashboard',
            compact(

                'suratMasuk',

                'suratKeluar',

                'disposisi',

                'disposisiAktif',

                'menunggu',

                'disposisiBelum',

                'prioritasTinggi',

                'suratTerbaru',

                'disposisiTerbaru',

                'aktivitasTerbaru'

            )
        );

    }

}
