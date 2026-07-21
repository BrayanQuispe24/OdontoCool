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
        Schema::create('servicio_prestados', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2);
            $table->date('fecha_servicio');
            $table->string('observacion')->nullable();
            $table->string('estado')->default('ACTIVO');
            
            // Foreign Keys
            $table->unsignedBigInteger('tratamiento_id');
            $table->foreign('tratamiento_id')->references('id')->on('tratamientos')->onDelete('cascade');
            
            $table->unsignedBigInteger('servicio_id');
            $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
            
            $table->unsignedBigInteger('id_boleta')->nullable();
            $table->foreign('id_boleta')->references('id_boleta')->on('boleta_pago')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_prestados');
    }
};
