<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Kabupaten extends Model
{
    protected $table = 'adms_kabupaten';
    protected $fillable = ['id', 'provinsi_kode', 'kota_kode', 'nama'];

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
