<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $fillable = ['id', 'title', 'content', 'image'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}

    public function getTglPublishAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y');
	}
}
