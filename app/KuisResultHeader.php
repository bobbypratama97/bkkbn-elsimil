<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisResultHeader extends Model
{
    protected $table = 'kuisioner_result_header';
    protected $fillable = ['id', 'result_id', 'nilai', 'pertanyaan_header_jenis', 'pertanyaan_header_caption', 'pertanyaan_header_formula'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
