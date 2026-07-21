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
        Schema::create('historiales_clinicos', function (Blueprint $table) {
            $table->id('codigo_historial');
            $table->text('alergias')->nullable();
            $table->text('antecedentes_medicos')->nullable();
            $table->text('enfermedades_base')->nullable();
            $table->string('motivo_apertura');
            $table->date('fecha_apertura');
            $table->date('fecha_actualizacion')->nullable();
            $table->text('observaciones_generales')->nullable();
            $table->string('estado')->default('ACTIVO');
            $table->string('paciente_ci', 15)->unique();
            $table->foreign('paciente_ci')->references('codigo_paciente')->on('pacientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiales_clinicos');
    }
};
