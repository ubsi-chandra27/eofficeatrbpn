<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'jabatan';
    

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * Jika tabel memiliki created_at & updated_at
     */
    public $timestamps = true;

    /**
     * Field yang boleh diisi
     */
    protected $fillable = [
    'kode',
    'nama',
    'deskripsi',
];
    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'jabatan_id');
    }
}
