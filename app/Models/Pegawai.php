<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';


    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'jabatan_id',
        'unit_kerja_id',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELASI USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | RELASI JABATAN
    |--------------------------------------------------------------------------
    */

    public function jabatan()
    {
        return $this->belongsTo(
            Jabatan::class,
            'jabatan_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | RELASI UNIT KERJA
    |--------------------------------------------------------------------------
    */

    public function unitKerja()
    {
        return $this->belongsTo(
            UnitKerja::class,
            'unit_kerja_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | DISPOSISI YANG DITERIMA PEGAWAI
    |--------------------------------------------------------------------------
    */

    public function disposisiTujuans()
    {
        return $this->hasMany(
            DisposisiTujuan::class,
            'pegawai_id'
        );
    }


    /**
     * Alias
     */
    public function disposisi()
    {
        return $this->disposisiTujuans();
    }

}
