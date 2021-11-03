<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Setting extends Model
{
    protected $table = 'setting';
    protected $fillable = ['id', 'jenis', 'tipe', 'name', 'value'];
    public $timestamps = false;

}
