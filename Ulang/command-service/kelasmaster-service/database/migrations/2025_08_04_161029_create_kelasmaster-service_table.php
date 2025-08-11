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
        Schema::create('kelasmaster', function (Blueprint $table) {
        $table->id();
        $table->string('kodekelas')->unique();
        $table->string('namakelas');
        $table->string('kodejurusan');
        $table->string('kodeprodi');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kelasmaster');
    }
};
