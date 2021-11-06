<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuisHamilNifasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuisioner_hamil_nifas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_user')->unsigned()->index();
            $table->bigInteger('id_member')->unsigned()->index();
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_member')->references('id')->on('members');
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
        Schema::dropIfExists('kuisioner_hamil_nifas');
    }
}
