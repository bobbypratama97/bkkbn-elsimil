<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamilIbuJanin extends Model
{
    protected $table = 'kuisioner_hamil_ibu_janin';
    protected $fillable = [
        'id_user',
        'id_member',
        'periode',
        'kenaikan_berat_badan',
        'hemoglobin',
        'tensi_darah',
        'gula_darah',
        'proteinuria',
        'denyut_jantung',
        'tinggi_fundus_uteri',
        'taksiran_berat_janin',
        'gerak_janin',
        'jumlah_janin'
       ];
}
