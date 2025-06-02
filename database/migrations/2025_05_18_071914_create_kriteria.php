<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->integer('no_kriteria');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->timestamps();

            $table->primary(['no_kriteria', 'id_user']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kriteria');
    }
};
