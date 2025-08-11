<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('kodematkul');
            $table->integer('nilaiangka');
            $table->timestamps();

            $table->unique(['nim', 'kodematkul']);

            // $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');
            // $table->foreign('kodematkul')->references('kodematkul')->on('matakuliah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
