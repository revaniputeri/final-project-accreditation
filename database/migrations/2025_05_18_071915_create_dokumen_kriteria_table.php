<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dokumen_kriteria', function (Blueprint $table) {
            $table->id('id_dokumen_kriteria');
            $table->integer('no_kriteria')->index();
            $table->integer('versi');
            $table->string('judul', 255);
            $table->enum('kategori', ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])->default('penetapan');
            $table->longText('content_html');
            $table->enum('status', ['tervalidasi', 'revisi', 'kosong', 'perlu validasi'])->default('kosong');
            $table->foreignId('id_validator')->nullable()->constrained('users');
            $table->text('komentar')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Relasi ke kriteria (tanpa id_user)
            $table->foreign('no_kriteria')
                ->references('no_kriteria')
                ->on('kriteria')
                ->onDelete('cascade');

            // $table->unique(['no_kriteria', 'versi', 'kategori']); // Satu versi per no_kriteria dan kategori
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokumen_kriteria');
    }
};
