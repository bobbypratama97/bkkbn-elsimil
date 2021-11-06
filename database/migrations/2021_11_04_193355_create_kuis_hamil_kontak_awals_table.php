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
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_member')->references('id')->on('members');
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
            $table->boolean('bansos');
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
        Schema::dropIfExists('kuis_hamil_kontak_awals');
    }
}
