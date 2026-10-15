<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espacios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo', ['Carro', 'Moto']);
            $table->decimal('tarifa', 10, 2);
            $table->enum('estado', ['Disponible', 'Ocupado', 'Mantenimiento'])->default('Disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espacios');
    }
};