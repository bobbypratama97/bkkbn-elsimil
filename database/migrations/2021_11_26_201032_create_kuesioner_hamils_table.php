<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuesionerHamilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuesioner_hamil', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_member');
            $table->integer('periode');
            $table->string('nama');
            $table->string('nik');
            $table->integer('usia');
            $table->string('alamat');
            $table->integer('jumlah_anak');
            $table->integer('usia_anak_terakhir');
            $table->boolean('anak_stunting');
            $table->date('hari_pertama_haid_terakhir');
            $table->boolean('sumber_air_bersih');
            $table->boolean('rumah_layak_huni');
            $table->boolean('jamban_sehat');
            $table->boolean('bansos');
            $table->double('berat_badan');
            $table->double('tinggi_badan');
            $table->double('lingkar_lengan_atas');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah');
            $table->boolean('riwayat_sakit_kronik');
            $table->integer('gula_darah_sewaktu');
            $table->double('kenaikan_berat_badan');
            $table->boolean('proteinuria');
            $table->integer('denyut_jantung');
            $table->integer('tinggi_fundus_uteri');
            $table->integer('taksiran_berat_janin');
            $table->boolean('gerak_janin');
            $table->integer('jumlah_janin');
            $table->date('tanggal_persalinan');
            $table->boolean('kb');
            $table->integer('usia_janin');
            $table->integer('berat_janin');
            $table->integer('panjang_badan_janin');
            $table->integer('jumlah_bayi');
            $table->boolean('komplikasi');
            $table->boolean('asi');
            $table->boolean('kbpp_mkjp');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kuesioner_hamils');
    }
}
