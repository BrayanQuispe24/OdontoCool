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
        Schema::create('personas', function (Blueprint $table) {
            $table->string('carnet_identidad', 10)->primary();
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->date('fecha_nacimiento');
            $table->string('direccion', 255)->default('Sin dirección');
            $table->string('genero', 15);
            $table->string('telefono', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
