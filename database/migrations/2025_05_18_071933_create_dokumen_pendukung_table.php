<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dokumen_pendukung', function (Blueprint $table) {
            $table->id('id_dokumen_pendukung');
            $table->string('no_kriteria', 50);
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokumen_pendukung');
    }
};
