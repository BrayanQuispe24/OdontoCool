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
        Schema::create('antecedentes_odontologicos', function (Blueprint $table) {
            $table->id('id_antecedente');
            $table->text('observacion_general')->nullable();
            $table->date('fecha_registro');
            $table->foreignId('codigo_historial')->unique()->constrained('historiales_clinicos', 'codigo_historial')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedentes_odontologicos');
    }
};
