<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisResultDetail extends Model
{
    protected $table = 'kuisioner_result_detail';
    protected $fillable = ['id', 'result_id', 'value', 'pertanyaan_detail_title', 'pertanyaan_detail_pilihan', 'pertanyaan_detail_bobot', 'pertanyaan_bobot_id', 'pertanyaan_bobot_kondisi', 'pertanyaan_bobot_label', 'pertanyaan_bobot_nilai', 'pertanyaan_bobot'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
