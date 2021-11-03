<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class FasKes extends Model
{
    protected $table = 'faskes';
    protected $fillable = ['id', 'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'faskes_kode', 'nama', 'alamat'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
