<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Member extends Authenticatable implements JWTSubject
{
	use Notifiable;

    protected $table = 'members';
    protected $fillable = [
        'name', 'no_telp', 'email', 'password', 'no_ktp', 'foto_ktp', 'tempat_lahir', 'tgl_lahir', 'gender', 'alamat',
        'kota_id', 'kecamatan_id', 'kelurahan_id', 'rt', 'rw', 'kodepos', 'is_active', 'profile_code'
    ];
    public $timestamps = false;

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }

}
