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
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');
            $table->date('fecha_cita');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('motivo');
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('paciente_ci', 15);
            $table->foreign('paciente_ci')->references('codigo_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreignId('codigo_historial')->nullable()->constrained('historiales_clinicos', 'codigo_historial');
            $table->string('secretaria_ci', 15)->nullable();
            $table->foreign('secretaria_ci')->references('codigo_secretaria')->on('secretarias')->onDelete('set null');
            $table->string('doctor_ci', 15);
            $table->foreign('doctor_ci')->references('codigo_doctor')->on('doctores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
