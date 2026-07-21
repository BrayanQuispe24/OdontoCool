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
        Schema::create('cuotas_multa', function (Blueprint $table) {
            $table->id('id_cuota_multa');
            $table->decimal('monto_multa', 10, 2);
            $table->date('fecha_generada');
            $table->string('motivo');
            $table->string('estado')->default('ACTIVO');
            
            // Foreign Key
            $table->unsignedBigInteger('id_cuota');
            $table->foreign('id_cuota')->references('id_cuota')->on('cuota_boleta')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_multa');
    }
};
