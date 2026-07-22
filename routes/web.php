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
        Route::patch('/trash/{type}/{id}/restore', 'restore')->name('trash.restore');
    });


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



    Route::resource(
        'surat-masuk',
        PegawaiSuratMasukController::class
    )
    ->parameters([
        'surat-masuk'=>'id'
    ])
    ->names('surat-masuk');



    Route::put(
        '/surat-masuk/{id}/kirim',
        [PegawaiSuratMasukController::class,'kirim']
    )
    ->name('surat-masuk.kirim');



    Route::resource(
        'surat-keluar',
        PegawaiSuratKeluarController::class
    );

    Route::put('/surat-keluar/{id}/kirim', [PegawaiSuratKeluarController::class, 'kirim'])
        ->name('surat-keluar.kirim');



    Route::get('/disposisi',
        [PegawaiDisposisiController::class,'index']
    )
    ->name('disposisi.index');

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

    Route::resource(
        'surat',
        UmumSuratController::class
    );


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

});
