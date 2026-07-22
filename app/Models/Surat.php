<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';

    protected $fillable = [

        'user_id',

        'jenis_surat',

        'nomor_surat',
        'tanggal_surat',
        'tanggal_keluar',
        'tanggal_kirim',

        'nomor_agenda',
        'kode_surat',

        'judul_surat',
        'perihal',
        'lampiran',

        'asal_surat',
        'tujuan_surat',

        'penandatangan',

        'metode',
        'deskripsi',

        'file_path',

        'is_priority',

        'status',
        'catatan_admin',

        'jabatan_pimpinan_id',
        'nama_pimpinan',

    ];

    protected $casts = [

        'tanggal_surat'  => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_kirim'  => 'date',

        'is_priority'    => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    /**
     * User pembuat surat
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Semua disposisi surat
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'surat_id');
    }

    /**
     * Semua log aktivitas surat
     */
    public function logs()
    {
        return $this->hasMany(LogAktivitas::class, 'surat_id')
                    ->latest();
    }

    /**
 * Semua tujuan disposisi surat
 */
public function disposisiTujuans()
{
    return $this->hasManyThrough(
        DisposisiTujuan::class,
        Disposisi::class,
        'surat_id',
        'disposisi_id',
        'id',
        'id'
    );
}

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    /**
     * Badge Bootstrap berdasarkan status
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {

            'menunggu' => 'warning',

            'diproses' => 'info',

            'selesai' => 'success',

            'ditolak' => 'danger',

            default => 'secondary',
        };
    }


    public function jabatanPimpinan()
{
    return $this->belongsTo(Jabatan::class,'jabatan_pimpinan_id');
}
    /**
     * Label status
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status ?? '-');
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPE
    |--------------------------------------------------------------------------
    */

    /**
     * Surat masuk
     */
    public function scopeMasuk($query)
    {
        return $query->where('jenis_surat', 'masuk');
    }

    /**
     * Surat keluar
     */
    public function scopeKeluar($query)
    {
        return $query->where('jenis_surat', 'keluar');
    }

    /**
     * Berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Berdasarkan user
     */
    public function scopeMilikUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}