<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class News extends Model
{
    protected $table = 'news';
    protected $fillable = ['id', 'kategori_id', 'title', 'thumbnail', 'deskripsi', 'content', 'status'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}

    public function getTglPublishAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y');
	}
}
