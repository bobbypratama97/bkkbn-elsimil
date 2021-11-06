<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamil16Minggu extends Model
{
    protected $table = 'kuisioner_hamil_kontak_awal';
    protected $fillable = [
    'id_user',
    'id_member',
    'hemoglobin',
    'tensi_darah',
    'gula_darah_sewaktu'
   ];
}
