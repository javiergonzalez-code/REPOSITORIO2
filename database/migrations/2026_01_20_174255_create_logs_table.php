<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 15)->nullable(); // Debe ser exactamente string de 15 caracteres
            $table->foreign('user_id')->references('CardCode')->on('users')->onDelete('cascade');
            $table->string('accion');  // Qué hizo (ej: "Subió archivo")
            $table->string('modulo');  // Dónde (ej: "INPUTS")
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
