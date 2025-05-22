<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('level', function (Blueprint $table) {
            $table->id('id_level');
            $table->string('nama_level', 100);
            $table->string('kode_level', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('level');
    }
};
