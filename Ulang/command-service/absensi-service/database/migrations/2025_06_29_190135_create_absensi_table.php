<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('kodekelas');
            $table->string('kodematkul');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa']);
            $table->timestamps();

            $table->unique(['nim', 'kodematkul', 'tanggal'], 'absensi_unique');
            // $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');

            // $table->foreign('kodematkul')->references('kodematkul')->on('matakuliah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
