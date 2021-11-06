<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name',25);
            $table->string('no_telp',20);
            $table->string('email',255);
            $table->timestamp('email_verified_at');
            $table->string('password',255);
            $table->string('no_ktp',250);
            $table->string('foto_ktp',255);
            $table->string('foto_pic',255);
            $table->string('tempat_lahir',200);
            $table->date('tgl_lahir');
            $table->char('gender',1);
            $table->string('alamat',255);
            $table->string('provinsi_id',10);
            $table->string('kabupaten_id',10)->index();
            $table->string('kecamatan_id',15)->index();
            $table->string('kelurahan_id',20)->index();
            $table->string('rt',5);
            $table->string('rw',5);
            $table->string('kodepos',5);
            $table->string('profile_code',10);
            $table->tinyInteger('is_active');
            $table->string('remember_token',100);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('deleted_by');
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
        Schema::dropIfExists('members');
    }
}
