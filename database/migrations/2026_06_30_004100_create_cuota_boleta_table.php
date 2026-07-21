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
        Schema::create('cuota_boleta', function (Blueprint $table) {
            $table->id('id_cuota');
            $table->integer('numero_cuota');
            $table->decimal('monto_cuota', 10, 2);
            $table->date('fecha_vencimiento');
            $table->date('fecha_pago')->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado')->default('PENDIENTE');
            $table->string('id_transaccion')->nullable();
            $table->string('comprobante')->nullable();
            
            // Foreign Keys
            $table->unsignedBigInteger('id_metodo_pago')->nullable();
            $table->foreign('id_metodo_pago')->references('id_metodo_pago')->on('metodo_pago');
            
            $table->unsignedBigInteger('id_boleta');
            $table->foreign('id_boleta')->references('id_boleta')->on('boleta_pago')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuota_boleta');
    }
};
