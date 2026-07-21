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
        Schema::create('detalle_antecedentes_odontologicos', function (Blueprint $table) {
            $table->id('id_detalle_antecedente');
            $table->string('nombre_tratamiento');
            $table->text('descripcion')->nullable();
            $table->date('fecha_tratamiento');
            $table->string('lugar_tratamiento');
            $table->text('observacion')->nullable();
            $table->foreignId('id_antecedente')->constrained('antecedentes_odontologicos', 'id_antecedente')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_antecedentes_odontologicos');
    }
};
