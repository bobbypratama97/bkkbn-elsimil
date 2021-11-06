<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamil12Minggu extends Model
{
    protected $table = 'kuisioner_hamil_12_minggu';
    protected $fillable = [
    'id_user',
    'id_member',
    'berat_badan',
    'tinggi_badan',
    'lingkar_lengan_atas',
    'hemoglobin',
    'tensi_darah',
    'gula_darah',
    'riwayat_sakit_kronik'
   ];
}
