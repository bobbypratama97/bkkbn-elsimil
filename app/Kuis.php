<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Kuis extends Model
{
    protected $table = 'kuisioner';
    protected $fillable = ['id', 'title', 'gender', 'deskripsi', 'thumbnail', 'image', 'apv', 'max_point', 'position'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
