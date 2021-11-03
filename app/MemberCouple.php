<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class MemberCouple extends Model
{
    protected $table = 'member_couple';
    protected $fillable = ['id', 'member_id', 'couple_id'];

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }
}
