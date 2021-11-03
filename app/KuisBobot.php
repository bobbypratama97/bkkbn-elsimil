<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisBobot extends Model
{
    protected $table = 'pertanyaan_bobot';
    protected $fillable = ['id', 'header_id', 'kondisi', 'label', 'nilai', 'bobot'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
