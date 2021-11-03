<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nik', 'name', 'email', 'password', 'no_ktp', 'foto_ktp', 'foto_pic', 'tempat_lahir', 'tgl_lahir', 'gender', 'alamat', 'profile_code', 'is_active', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    public function getRoleAttribute() {
        $role = DB::table('role_user')->where('user_id', $this->id)->value('role_id');
        return $role;
    }

}
