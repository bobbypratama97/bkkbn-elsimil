<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class ChatHeader extends Model
{
    protected $table = 'chat_header';
    protected $fillable = ['member_id', 'responder_id', 'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'kelurahan_kode', 'status'];
    public $timestamps = false;

    public function user() {
    	return $this->belongsTo(User::class);
    }

    //public function getCreatedAtAttribute($date) {
    //	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	//}
}
