<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class NewsKategori extends Model
{
    protected $table = 'news_kategori';
    protected $fillable = ['id', 'name', 'deskripsi', 'thumbnail', 'color', 'status'];
    public $timestamps = false;

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }

}
