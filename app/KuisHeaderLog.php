<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisHeaderLog extends Model
{
    protected $table = 'pertanyaan_header_log';
    protected $fillable = ['id', 'kuis_id', 'pertanyaan_header_id', 'jenis', 'deskripsi', 'caption', 'formula'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
