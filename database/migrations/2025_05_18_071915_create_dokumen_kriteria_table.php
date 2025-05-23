<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dokumen_kriteria', function (Blueprint $table) {
            $table->id('id_dokumen_kriteria');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('judul', 255);
            $table->longText('content_html');
            $table->enum('status', ['tervalidasi', 'revisi', 'kosong'])->default('kosong');
            $table->foreignId('id_validator')->nullable()->constrained('user', 'id_user');
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokumen_kriteria');
    }
};

