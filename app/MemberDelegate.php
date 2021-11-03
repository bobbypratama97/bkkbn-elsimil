<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class MemberDelegate extends Model
{
    protected $table = 'member_delegate';
    protected $fillable = ['id', 'member_id', 'user_id', 'status'];

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }
}
