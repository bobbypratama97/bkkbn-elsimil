<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class MemberDelegateLog extends Model
{
    protected $table = 'member_delegate_log';
    protected $fillable = ['id', 'member_id', 'user_id'];

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }
}
