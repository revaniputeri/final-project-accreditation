<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dokumen_pendukung', function (Blueprint $table) {
            $table->id('id_dokumen_pendukung');
            $table->integer('no_kriteria');
            $table->enum('kategori', ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])->default('penetapan');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->text('keterangan');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key composite ke tabel kriteria
            $table->foreign(['no_kriteria', 'id_user'])
                  ->references(['no_kriteria', 'id_user'])
                  ->on('kriteria')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokumen_pendukung');
    }
};
