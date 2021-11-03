<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Apply extends Model
{
    protected $table = 'kuisioner_approval';
    protected $fillable = ['id', 'kuis_id', 'filer_by', 'proceed_by', 'catatan', 'description', 'status'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
