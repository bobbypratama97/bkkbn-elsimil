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
            $table->bigInteger('id_user')->unsigned()->index();
            $table->bigInteger('id_member')->unsigned()->index();
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_member')->references('id')->on('members');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah_sewaktu');
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
        Schema::dropIfExists('kuisioner_hamil_16_minggu');
    }
}
