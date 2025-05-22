<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profile_user', function (Blueprint $table) {
            $table->id('id_profile');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->string('nama_lengkap', 100);
            $table->string('tempat_tanggal_lahir', 100);
            $table->string('nidn', 20);
            $table->string('nip', 20);
            $table->string('gelar_depan', 20)->nullable();
            $table->string('gelar_belakang', 20)->nullable();
            $table->string('pendidikan_terakhir', 50);
            $table->string('pangkat', 50);
            $table->string('jabatan_fungsional', 100);
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profile_user');
    }
};
