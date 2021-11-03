<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Rwrt extends Model
{
    protected $table = 'adms_rw_rt';
    protected $fillable = ['id', 'kelurahan_kode', 'kode_rw', 'rw', 'kode_rt', 'rt'];

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
