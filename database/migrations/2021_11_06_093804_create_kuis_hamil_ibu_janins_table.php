<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuisHamilIbuJaninsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuisioner_hamil_ibu_janin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_member');
            $table->integer('periode'); #minggu kehamilan
            $table->double('kenaikan_berat_badan');
            $table->integer('hemoglobin');
            $table->integer('tensi_darah');
            $table->integer('gula_darah');
            $table->boolean('proteinuria');
            $table->integer('denyut_jantung');
            $table->integer('tinggi_fundus_uteri');
            $table->integer('taksiran_berat_janin');
            $table->boolean('gerak_janin');
            $table->integer('jumlah_janin');
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
        Schema::dropIfExists('kuis_hamil_ibu_janins');
    }
}
