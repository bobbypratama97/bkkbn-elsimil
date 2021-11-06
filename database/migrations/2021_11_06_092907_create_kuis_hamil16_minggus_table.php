<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuisHamil16MinggusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuisioner_hamil_16_minggu', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_member');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah_sewaktu');
            $table->softDeletes();
            $table->timestamps();
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
        Schema::dropIfExists('kuis_hamil16_minggus');
    }
}
