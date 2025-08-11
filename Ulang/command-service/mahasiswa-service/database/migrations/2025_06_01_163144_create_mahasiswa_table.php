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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('tempatlahir');
            $table->date('tanggallahir');
            $table->enum('jeniskelamin', ['L', 'P']);
            $table->string('kodejurusan');
            $table->string('kodeprodi');
            $table->timestamps();

            // $table->foreign('kodejurusan')->references('kodejurusan')->on('jurusan')->onDelete('cascade');
            // $table->foreign('kodeprodi')->references('kodeprodi')->on('prodi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahasiswa');
    }
};
