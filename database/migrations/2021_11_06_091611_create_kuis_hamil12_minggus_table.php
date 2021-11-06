<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuisHamil12MinggusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuisioner_hamil_12_minggu', function (Blueprint $table) {
            $table->id();
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_member')->references('id')->on('members');
            $table->double('berat_badan');
            $table->double('tinggi_badan');
            $table->double('lingkar_lengan_atas');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah');
            $table->boolean('riwayat_sakit_kronik');
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
        Schema::dropIfExists('kuis_hamil12_minggus');
    }
}
