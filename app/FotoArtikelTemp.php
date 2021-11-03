<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class FotoArtikelTemp extends Model
{
    protected $table = 'news_image_temp';
    protected $fillable = ['id', 'user_id', 'artikel_id', 'tipe', 'filename'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}

    public function getTglPublishAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y');
	}
}
