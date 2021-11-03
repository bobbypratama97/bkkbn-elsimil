<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Kelurahan extends Model
{
    protected $table = 'adms_kelurahan';
    protected $fillable = ['id', 'kecamatan_kode', 'kelurahan_kode', 'nama'];

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
