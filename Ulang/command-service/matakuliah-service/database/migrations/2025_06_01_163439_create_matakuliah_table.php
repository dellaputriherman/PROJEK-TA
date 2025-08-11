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
        Schema::create('matakuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kodematkul')->unique();
            $table->string('namamatkul');
            $table->integer('semester');
            $table->integer('sks');
            $table->integer('jam');
            $table->string('kodeprodi');
            $table->timestamps();

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
        Schema::dropIfExists('matakuliah');
    }
};
