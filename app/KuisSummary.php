<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisSummary extends Model
{
    protected $table = 'kuisioner_summary';
    protected $fillable = ['id', 'kuis_id', 'kondisi', 'label', 'nilai', 'deskripsi'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
