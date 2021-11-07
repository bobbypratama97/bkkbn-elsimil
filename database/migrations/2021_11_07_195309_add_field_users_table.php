<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik',250);
            $table->string('provinsi_id',10);
            $table->string('kabupaten_id',10);
            $table->string('kecamatan_id',15);
            $table->string('kelurahan_id',20);
            $table->tinyInteger('is_active');
            $table->tinyInteger('chat_active_status');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamp('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('nik');
            $table->dropColumn('provinsi_id');
            $table->dropColumn('kabupaten_id');
            $table->dropColumn('kecamatan_id');
            $table->dropColumn('kelurahan_id');
            $table->dropColumn('is_active');
            $table->dropColumn('chat_active_status');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_by');
            $table->dropColumn('deleted_at');
        });
    }
}
