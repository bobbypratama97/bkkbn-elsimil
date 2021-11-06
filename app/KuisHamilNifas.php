<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamilNifas extends Model
{
    protected $table = 'kuisioner_hamil_nifas';
    protected $fillable = [
        'id_user',
        'id_member',
        'komplikasi',
        'asi',
        'kbpp_mkjp'
       ];
}
