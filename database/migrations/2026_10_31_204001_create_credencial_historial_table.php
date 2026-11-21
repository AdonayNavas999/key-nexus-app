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
        Schema::create('credencial_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credencial_id')->constrained('credencial')->cascadeOnDelete();
            $table->text('contrasena_anterior'); // Contraseña previa cifrada con AES-256
            $table->text('contrasena_nueva');    // Contraseña nueva cifrada con AES-256
            $table->text('motivo');              // Motivo del cambio de contraseña
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credencial_historial');
    }
};
