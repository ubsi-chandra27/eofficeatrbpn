<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuratLampiranController;


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPegawaiController;
use App\Http\Controllers\Admin\AdminJabatanController;
use App\Http\Controllers\Admin\AdminUnitKerjaController;
use App\Http\Controllers\Admin\AdminSuratMasukController;
use App\Http\Controllers\Admin\AdminSuratKeluarController;
use App\Http\Controllers\Admin\AdminDisposisiController;


/*
|--------------------------------------------------------------------------
| PEGAWAI
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\SuratMasukController as PegawaiSuratMasukController;
use App\Http\Controllers\Pegawai\SuratKeluarController as PegawaiSuratKeluarController;
use App\Http\Controllers\Pegawai\DisposisiController as PegawaiDisposisiController;


/*
|--------------------------------------------------------------------------
| UMUM
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Umum\DashboardController as UmumDashboardController;
use App\Http\Controllers\Umum\UmumSuratController;


/*
|--------------------------------------------------------------------------
| LAINNYA
|--------------------------------------------------------------------------
*/





/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function(){

    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');

});



/*
|--------------------------------------------------------------------------
| REDIRECT DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function(){


    if(!auth()->check()){

        return redirect()->route('login');

    }


    return match(auth()->user()->role){


        'admin'
            => redirect()->route('admin.dashboard'),


        'pegawai'
            => redirect()->route('pegawai.dashboard'),


        'umum'
            => redirect()->route('umum.dashboard'),


        default
            => abort(403)

    };


})
->middleware('auth')
->name('dashboard.index');





/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/


Route::middleware('guest')->group(function(){


    Route::get('/login',
        [AuthenticatedSessionController::class,'create'])
        ->name('login');


    Route::post('/login',
        [AuthenticatedSessionController::class,'store']);



    Route::get('/register',
        [RegisteredUserController::class,'create'])
        ->name('register');



    Route::post('/register',
        [RegisteredUserController::class,'store'])
        ->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');


});



Route::post('/logout',
    [AuthenticatedSessionController::class,'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
});





/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/


Route::middleware('auth')->group(function(){

    Route::get('/notifications/{id}/open', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/surat/{surat}/lampiran', SuratLampiranController::class)
        ->whereNumber('surat')
        ->name('surat.lampiran');


    Route::get('/profile',
        [ProfileController::class,'edit'])
        ->name('profile.edit');


    Route::patch('/profile',
        [ProfileController::class,'update'])
        ->name('profile.update');


    Route::put('/profile/password',
        [ProfileController::class,'updatePassword'])
        ->name('profile.password.update');

    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])
        ->name('profile.photo.update');

    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])
        ->name('profile.photo.destroy');


    Route::delete('/profile',
        [ProfileController::class,'destroy'])
        ->name('profile.destroy');


});





/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/


Route::middleware(['auth','role:admin'])
->prefix('admin')
->name('admin.')
->group(function(){



    Route::get('/dashboard',
        [AdminDashboardController::class,'index'])
        ->name('dashboard');



    Route::resource(
        'surat-masuk',
        AdminSuratMasukController::class
    )
    ->names('surat.masuk');


    Route::prefix('surat-masuk')
    ->name('surat.masuk.')
    ->group(function () {

        Route::post(
            '{id}/setujui',
            [AdminSuratMasukController::class, 'setujui']
        )
        ->name('setujui');

        Route::post(
            '{id}/tolak',
            [AdminSuratMasukController::class, 'tolak']
        )
        ->name('tolak');

        Route::post(
            '{id}/teruskan-pimpinan',
            [AdminSuratMasukController::class, 'teruskanKePimpinan']
        )
        ->name('teruskan-pimpinan');

    });



    Route::resource(
        'surat-keluar',
        AdminSuratKeluarController::class
    )
    ->names('surat.keluar');

    Route::prefix('surat-keluar')
    ->name('surat.keluar.')
    ->group(function () {

        Route::post(
            '{id}/setujui',
            [AdminSuratKeluarController::class, 'setujui']
        )
        ->name('setujui');

        Route::post(
            '{id}/tolak',
            [AdminSuratKeluarController::class, 'tolak']
        )
        ->name('tolak');

    });



    Route::resource(
        'disposisi',
        AdminDisposisiController::class
    );



    Route::resource(
        'pegawai',
        AdminPegawaiController::class
    );



    Route::resource(
        'jabatan',
        AdminJabatanController::class
    );



    Route::resource(
        'unitkerja',
        AdminUnitKerjaController::class
    )
    ->names('unit.kerja');



    Route::controller(AdminController::class)->group(function(){


        Route::get('/laporan',
            'laporan')
            ->name('laporan.index');



        Route::get('/users', 'userIndex')->name('users.index');
        Route::patch('/users/{id}/role', 'updateUserRole')->name('users.updateRole');
        Route::patch('/users/{id}/nip', 'updateUserNip')->name('users.updateNip');
        Route::patch('/users/{id}/password', 'resetUserPassword')->name('users.resetPassword');
        Route::delete('/users/{id}', 'destroyUser')->name('users.destroy');


    });

    Route::controller(\App\Http\Controllers\Admin\AdminSettingsController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
        Route::get('/profile', 'profile')->name('profile');
        Route::get('/security', 'security')->name('security');
        Route::get('/instansi', 'instansi')->name('instansi');
        Route::get('/format', 'format')->name('format');
        Route::put('/format', 'updateFormat')->name('format.update');
        Route::get('/backup', 'backup')->name('backup');
        Route::post('/backup/download', 'downloadBackup')->name('backup.download');
        Route::post('/backup/restore', 'restoreBackup')->name('backup.restore');
        Route::get('/about', 'about')->name('about');
        Route::patch('/trash/{type}/{id}/restore', 'restore')->name('trash.restore');
    });

    Route::resource('berita', \App\Http\Controllers\Admin\AdminBeritaController::class);


});

/*
|--------------------------------------------------------------------------
| PEGAWAI
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:pegawai'])
->prefix('pegawai')
->name('pegawai.')
->group(function(){


    Route::get('/dashboard',
        [PegawaiDashboardController::class,'index']
    )
    ->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'pegawaiIndex'])
        ->name('profile.index');

    Route::get('/profil/password', [ProfileController::class, 'pegawaiPassword'])
        ->name('profile.password');

    Route::get('/pengaturan', [ProfileController::class, 'pegawaiSettings'])
        ->name('settings.index');



    Route::resource(
        'surat-masuk',
        PegawaiSuratMasukController::class
    )
    ->except(['store'])
    ->parameters([
        'surat-masuk'=>'id'
    ])
    ->names('surat-masuk');

    Route::post('/surat-masuk', [PegawaiSuratMasukController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('surat-masuk.store');



    Route::put(
        '/surat-masuk/{id}/kirim',
        [PegawaiSuratMasukController::class,'kirim']
    )
    ->name('surat-masuk.kirim');

    Route::get('/surat-masuk/{id}/cetak', [PegawaiSuratMasukController::class, 'cetak'])
        ->whereNumber('id')
        ->name('surat-masuk.cetak');



    Route::resource(
        'surat-keluar',
        PegawaiSuratKeluarController::class
    )
    ->except(['store']);

    Route::post('/surat-keluar', [PegawaiSuratKeluarController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('surat-keluar.store');

    Route::put('/surat-keluar/{id}/kirim', [PegawaiSuratKeluarController::class, 'kirim'])
        ->name('surat-keluar.kirim');

    Route::get('/surat-keluar/{id}/cetak', [PegawaiSuratKeluarController::class, 'cetak'])
        ->whereNumber('id')
        ->name('surat-keluar.cetak');



    Route::get('/disposisi',
        [PegawaiDisposisiController::class,'index']
    )
    ->name('disposisi.index');

    Route::get('/disposisi/create', [PegawaiDisposisiController::class, 'create'])
        ->name('disposisi.create');
    Route::post('/disposisi', [PegawaiDisposisiController::class, 'store'])
        ->name('disposisi.store');
    Route::get('/disposisi/terkirim', [PegawaiDisposisiController::class, 'terkirim'])
        ->name('disposisi.terkirim');
    Route::get('/disposisi-terkirim/{id}', [PegawaiDisposisiController::class, 'sentShow'])
        ->name('disposisi.sent.show');
    Route::get('/disposisi-terkirim/{id}/edit', [PegawaiDisposisiController::class, 'edit'])
        ->name('disposisi.edit');
    Route::put('/disposisi-terkirim/{id}', [PegawaiDisposisiController::class, 'update'])
        ->name('disposisi.update');
    Route::delete('/disposisi-terkirim/{id}', [PegawaiDisposisiController::class, 'destroy'])
        ->name('disposisi.destroy');

    Route::get('/disposisi/{id}', [PegawaiDisposisiController::class, 'show'])
        ->name('disposisi.show');

    Route::get('/disposisi/{id}/cetak', [PegawaiDisposisiController::class, 'cetak'])
        ->name('disposisi.cetak');

    Route::patch('/disposisi/{id}/dibaca', [PegawaiDisposisiController::class, 'dibaca'])
        ->name('disposisi.dibaca');

    Route::patch('/disposisi/{id}/selesai', [PegawaiDisposisiController::class, 'selesai'])
        ->name('disposisi.selesai');


});
/*
|--------------------------------------------------------------------------
| UMUM
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:umum'])
->prefix('umum')
->name('umum.')
->group(function(){

    Route::get('/dashboard',
        [UmumDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/surat/{id}/download',
        [UmumSuratController::class, 'download'])
        ->whereNumber('id')
        ->name('surat.download');

    Route::post('/surat', [UmumSuratController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('surat.store');

    Route::resource('surat', UmumSuratController::class)
        ->except('store');


    Route::get('/cari',
    [UmumSuratController::class,'cariBerkasForm'])
    ->name('cari.form');

    Route::post('/cari',
    [UmumSuratController::class,'cariBerkas'])
    ->name('cari.proses');

    Route::get('/layanan',
        [UmumSuratController::class, 'layanan'])
        ->name('layanan.index');

    Route::get('/layanan/{layanan}',
        [UmumSuratController::class, 'detailLayanan'])
        ->whereIn('layanan', ['informasi', 'dokumen', 'penyampaian-surat', 'pengaduan', 'lainnya'])
        ->name('layanan.show');

    Route::view('/informasi/menteri', 'umum.menteri')->name('menteri');
    Route::view('/informasi/wakil-menteri', 'umum.wakil-menteri')->name('wakil');
    Route::view('/informasi/struktur-organisasi', 'umum.struktur')->name('struktur');
    Route::view('/informasi/profil-instansi', 'umum.profil-instansi')->name('profil-instansi');
    Route::view('/informasi/visi', 'umum.visi')->name('visi');
    Route::view('/informasi/misi', 'umum.misi')->name('misi');
    Route::view('/informasi/makna-logo', 'umum.makna-logo')->name('makna-logo');

    Route::get('/berita', [\App\Http\Controllers\Umum\BeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/{id}', [\App\Http\Controllers\Umum\BeritaController::class, 'show'])->name('berita.show');

});
