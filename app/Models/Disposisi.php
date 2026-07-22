<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disposisi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * --------------------------------------------------------------------------
     * Nama Tabel
     * --------------------------------------------------------------------------
     */
    protected $table = 'disposisi';

    /**
     * --------------------------------------------------------------------------
     * Mass Assignment
     * --------------------------------------------------------------------------
     */
    protected $fillable = [

        'surat_id',

        'pengirim_id',

        'catatan',

        'prioritas',

        'tanggal_disposisi',

    ];

    /**
     * --------------------------------------------------------------------------
     * Casting
     * --------------------------------------------------------------------------
     */
    protected $casts = [

        'tanggal_disposisi' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    /**
     * Surat yang didisposisikan
     */
    public function surat()
    {
        return $this->belongsTo(
            Surat::class,
            'surat_id'
        );
    }

    /**
     * User/Admin pengirim disposisi
     */
    public function pengirim()
    {
        return $this->belongsTo(
            User::class,
            'pengirim_id'
        );
    }

    /**
     * Seluruh tujuan disposisi
     *
     * Digunakan pada:
     * AdminDisposisiController
     * ->with('tujuans.pegawai')
     */
    public function tujuans()
    {
        return $this->hasMany(
            DisposisiTujuan::class,
            'disposisi_id'
        );
    }

    /**
     * Alias lama
     *
     * Agar kode lama yang masih memakai:
     * $disposisi->tujuan
     * tetap berjalan.
     */
    public function tujuan()
    {
        return $this->tujuans();
    }
}
