<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_organisasi', function (Blueprint $table) {
            $table->id('id_organisasi');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('nama_organisasi', 255);
            $table->string('kurun_waktu', 100);
            $table->enum('tingkat', ['Nasional', 'Internasional']);
            $table->enum('status', ['tervalidasi', 'perlu validasi', 'tidak valid'])->default('tervalidasi');
            $table->enum('sumber_data', ['p3m', 'dosen'])->default('dosen');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_organisasi');
    }
};
