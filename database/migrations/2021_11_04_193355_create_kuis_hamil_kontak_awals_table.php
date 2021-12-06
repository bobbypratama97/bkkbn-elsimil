<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuisHamilKontakAwalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuisioner_hamil_kontak_awal', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_member');
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
        Schema::dropIfExists('kuisioner_hamil_kontak_awal');
    }
}
