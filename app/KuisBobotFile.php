<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class KuisBobotFile extends Model
{
    protected $table = 'pertanyaan_bobot_file';
    protected $fillable = ['id', 'pertanyaan_bobot_id', 'name', 'file', 'ekstensi'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
