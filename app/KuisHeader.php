<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisHeader extends Model
{
    protected $table = 'pertanyaan_header';
    protected $fillable = ['id', 'kuis_id', 'jenis', 'deskripsi', 'caption', 'formula'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
