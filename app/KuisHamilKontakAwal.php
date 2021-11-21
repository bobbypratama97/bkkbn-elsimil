<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHamilKontakAwal extends Model
{
    protected $table = 'kuisioner_hamil_kontak_awal';
    protected $fillable = [
    'id_user',
    'id_member',
    'nama',
    'nik',
    'usia',
    'alamat',
    'jumlah_anak',
    'usia_anak_terakhir',
    'anak_stunting',
    'hari_pertama_haid_terakhir',
    'sumber_air_bersih',
    'rumah_layak_huni',
    'bansos'
   ];

}
