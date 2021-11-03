<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Kecamatan extends Model
{
    protected $table = 'adms_kecamatan';
    protected $fillable = ['id', 'kabupaten_kode', 'kecamatan_kode', 'nama'];

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
