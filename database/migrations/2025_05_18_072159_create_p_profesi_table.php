<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_profesi', function (Blueprint $table) {
            $table->id('id_profesi');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('perguruan_tinggi', 255);
            $table->string('kurun_waktu', 100);
            $table->string('gelar', 50);
            $table->enum('status', ['tervalidasi', 'perlu validasi', 'tidak valid'])->default('tervalidasi');
            $table->enum('sumber_data', ['p3m', 'dosen'])->default('dosen');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_profesi');
    }
};
