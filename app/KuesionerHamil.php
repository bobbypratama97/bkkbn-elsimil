<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuesionerHamil extends Model
{
    protected $table = 'kuesioner_hamil';

    /*
    nanti setiap ngubah kuesioner ke tabel ini
    fillable nya jangan lupa ditambah ya
    */

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
    'bansos',
    'periode'
   ];

}
