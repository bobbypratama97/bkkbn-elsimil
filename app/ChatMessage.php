<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class ChatMessage extends Model
{
    protected $table = 'chat_message';
    protected $fillable = ['id', 'chat_id', 'member_id', 'response_id', 'message', 'status'];

    /*public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}*/
}
