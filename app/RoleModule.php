<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class RoleModule extends Model
{
    protected $table = 'module_role';
    public $timestamps = false;
    protected $fillable = ['role_id', 'module_id'];
}
