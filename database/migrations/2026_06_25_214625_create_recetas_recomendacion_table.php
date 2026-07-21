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
        Schema::create('recetas_recomendacion', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_emision');
            $table->text('observacion_general')->nullable();
            $table->foreignId('tratamiento_id')->unique()->constrained('tratamientos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas_recomendacion');
    }
};
