<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Penduduk extends Model
{
    protected $table = 'adms_penduduk';
    protected $fillable = ['id', 'nik', 'nama', 'tgl_lahir', 'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'kelurahan_kode', 'rw_kode', 'rt_kode', 'kki', 'hubungan_kki'];

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
