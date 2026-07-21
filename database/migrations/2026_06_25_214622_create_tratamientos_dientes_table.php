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
        Schema::create('tratamientos_dientes', function (Blueprint $table) {
            $table->id();
            $table->string('cara_dental');
            $table->text('observacion')->nullable();
            $table->date('fecha_registro');
            $table->string('tratamiento_planificado');
            $table->string('estado')->default('ACTIVO');
            $table->foreignId('diente_id')->constrained('dientes')->onDelete('cascade');
            $table->foreignId('tratamiento_id')->constrained('tratamientos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos_dientes');
    }
};
