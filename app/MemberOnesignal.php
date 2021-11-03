<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class MemberOnesignal extends Model
{
    protected $table = 'member_onesignal';
    protected $fillable = ['id', 'member_id', 'player_id', 'imei', 'status'];

    public function getCreatedAtAttribute($date) {
        return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
    }
}
