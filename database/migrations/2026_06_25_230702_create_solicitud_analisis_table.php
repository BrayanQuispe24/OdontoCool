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
        Schema::create('solicitud_analisis', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_solicitud');
            $table->text('motivo')->nullable();
            $table->string('estado')->default('ACTIVO');
            $table->foreignId('analisis_id')->constrained('analisis')->onDelete('cascade');
            $table->foreignId('tratamiento_id')->constrained('tratamientos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_analisis');
    }
};
