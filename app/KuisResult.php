<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisResult extends Model
{
    protected $table = 'kuisioner_result';
    protected $fillable = ['id', 'kuis_code', 'member_id', 'responder_id', 'kuis_id', 'kuis_title', 'kuis_gender', 'kuis_max_nilai', 'member_kuis_nilai', 'summary_id', 'label', 'deskripsi', 'rating', 'rating_color', 'filename', 'status'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
