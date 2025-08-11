<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwalkuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kodekelas');
            $table->string('kodematkul');
            $table->string('hari');
            $table->time('jammulai');
            $table->time('jamselesai');
            $table->string('ruangan')->nullable();
            $table->string('nip'); // dosen
            $table->timestamps();
            $table->unique(['kodekelas', 'kodematkul']);

            // Foreign keys (optional, aktifkan jika semua relasi tersedia)
            // $table->foreign('kodekelas')->references('kodekelas')->on('kelasmaster');
            // $table->foreign('kodematkul')->references('kodematkul')->on('matakuliah');
            // $table->foreign('nip')->references('nip')->on('dosen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwalkuliah');
    }
};
