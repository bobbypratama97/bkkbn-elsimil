<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisResultBobotFile extends Model
{
    protected $table = 'kuisioner_result_bobot_file';
    protected $fillable = ['id', 'result_id', 'pertanyaan_bobot_id', 'name', 'file'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
