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
        Schema::create('boleta_pago', function (Blueprint $table) {
            $table->id('id_boleta');
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->date('fecha_emision');
            $table->decimal('total', 10, 2);
            $table->string('estado_pago')->default('PENDIENTE');
            
            // Foreign Keys
            $table->unsignedBigInteger('id_modo_pago');
            $table->foreign('id_modo_pago')->references('id_modo_pago')->on('modo_pago');
            
            $table->string('paciente_ci', 15);
            $table->foreign('paciente_ci')->references('codigo_paciente')->on('pacientes');
            
            $table->string('secretaria_ci', 15)->nullable();
            $table->foreign('secretaria_ci')->references('codigo_secretaria')->on('secretarias');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boleta_pago');
    }
};
