<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_kegiatan', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->enum('jenis_kegiatan', ['Lokakarya', 'Workshop', 'Pagelaran', 'Peragaan', 'Pelatihan', 'Lain_lain']);
            $table->string('tempat', 100);
            $table->date('waktu');
            $table->enum('peran', ['penyaji', 'peserta', 'lainnya']);
            $table->enum('status', ['tervalidasi', 'perlu validasi', 'tidak valid'])->default('tervalidasi');
            $table->enum('sumber_data', ['p3m', 'dosen'])->default('dosen');
            $table->string('bukti')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_kegiatan');
    }
};
