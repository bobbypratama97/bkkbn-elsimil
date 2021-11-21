<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Logbook extends Model
{
    protected $table = 'logbooks';
    protected $fillable = ['id', 'id_user', 'id_member', 'kie', 'suplemen_darah', 'suplemen_makanan','rujukan'];
    
}