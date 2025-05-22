<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_publikasi', function (Blueprint $table) {
            $table->id('id_publikasi');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('judul', 255);
            $table->string('tempat_publikasi', 100);
            $table->year('tahun_publikasi');
            $table->enum('jenis_publikasi', ['jurnal', 'prosiding', 'poster']);
            $table->decimal('dana', 15, 2);
            $table->boolean('melibatkan_mahasiswa_s2');
            $table->enum('status', ['tervalidasi', 'perlu validasi', 'tidak valid'])->default('tervalidasi');
            $table->enum('sumber_data', ['p3m', 'dosen'])->default('dosen');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_publikasi');
    }
};
