<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class MemberCoupleApproval extends Model
{
    protected $table = 'member_couple_approval';
    protected $fillable = ['id', 'member_couple_id', 'status', 'step'];

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }
}
