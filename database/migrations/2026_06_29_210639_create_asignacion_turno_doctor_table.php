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
        Schema::create('asignacion_turno_doctor', function (Blueprint $table) {
            $table->id();
            $table->string('dia_semana', 20);
            $table->string('doctor_id', 20);
            $table->foreign('doctor_id')->references('codigo_doctor')->on('doctores')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_turno_doctor');
    }
};
