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
            $table->string('anak_stunting');
            $table->date('hari_pertama_haid_terakhir');
            $table->string('sumber_air_bersih');
            $table->string('rumah_layak_huni');
            $table->string('jamban_sehat');
            $table->string('bansos');
            $table->double('berat_badan');
            $table->double('tinggi_badan');
            $table->double('lingkar_lengan_atas');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah');
            $table->string('riwayat_sakit_kronik');
            $table->integer('gula_darah_sewaktu');
            $table->double('kenaikan_berat_badan');
            $table->string('proteinuria');
            $table->integer('denyut_jantung');
            $table->integer('tinggi_fundus_uteri');
            $table->integer('taksiran_berat_janin');
            $table->string('gerak_janin');
            $table->integer('jumlah_janin');
            $table->date('tanggal_persalinan');
            $table->string('kb');
            $table->integer('usia_janin');
            $table->integer('berat_janin');
            $table->integer('panjang_badan_janin');
            $table->integer('jumlah_bayi');
            $table->string('komplikasi');
            $table->string('asi');
            $table->string('kbpp_mkjp');
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
