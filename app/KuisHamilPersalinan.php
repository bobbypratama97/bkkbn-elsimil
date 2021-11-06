<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamilPersalinan extends Model
{
    protected $table = 'kuisioner_hamil_persalinan';
    protected $fillable = [
        'id_user',
        'id_member',
        'tanggal_persalinan',
        'kb',
        'usia',
        'berat',
        'panjang_badan',
        'jumlah'
       ];
}
