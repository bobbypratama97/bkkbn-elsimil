<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeInChatHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_header', function (Blueprint $table) {
            $table->integer('type')->nullable()->after('status')->comment('type 1:ke petugas kb, 2: ke petugas pkk, 3: ke petugas bidan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_header', function (Blueprint $table) {
            //
        });
    }
}
