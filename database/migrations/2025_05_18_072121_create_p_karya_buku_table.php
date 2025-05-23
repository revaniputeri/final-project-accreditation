<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_karya_buku', function (Blueprint $table) {
            $table->id('id_karya_buku');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('judul_buku', 255);
            $table->year('tahun');
            $table->integer('jumlah_halaman');
            $table->string('penerbit', 100);
            $table->string('isbn', 50);
            $table->enum('status', ['tervalidasi', 'perlu validasi', 'tidak valid'])->default('tervalidasi');
            $table->enum('sumber_data', ['p3m', 'dosen'])->default('dosen');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_karya_buku');
    }
};
