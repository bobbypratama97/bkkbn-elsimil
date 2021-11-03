<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisDetailLog extends Model
{
    protected $table = 'pertanyaan_detail_log';
    protected $fillable = ['id', 'header_id', 'detail_id', 'title', 'pilihan', 'bobot'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
