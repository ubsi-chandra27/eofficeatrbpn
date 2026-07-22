<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'nip',
        'email',
        'phone',
        'organization',
        'address',
        'profile_photo_path',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pegawai()
{
    return $this->hasOne(
        Pegawai::class,
        'user_id'
    );
}
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }
    /**
 * Disposisi yang dikirim oleh user/admin
 */
public function disposisis()
{
    return $this->hasMany(
        Disposisi::class,
        'pengirim_id'
    );
}

}
