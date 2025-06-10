<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->integer('no_kriteria');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['no_kriteria', 'id_user']);

            // $table->unique(['no_kriteria', 'id_user']); // Ini menjaga agar tidak ada duplikat no_kriteria per user
        });
    }

    public function down()
    {
        Schema::dropIfExists('kriteria');
    }
};
