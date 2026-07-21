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
        Schema::create('resultado_analisis', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_resultado');
            $table->text('resultado');
            $table->text('observaciones')->nullable();
            $table->text('interpretacion')->nullable();
            $table->string('archivo_adjunto')->nullable();
            $table->string('estado')->default('ACTIVO');
            $table->foreignId('solicitud_analisis_id')->unique()->constrained('solicitud_analisis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultado_analisis');
    }
};
