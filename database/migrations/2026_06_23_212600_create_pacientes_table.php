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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->string('codigo_paciente', 15)->primary();
            $table->string('contacto_emergencia', 20);
            $table->string('telefono_emergencia', 20);
            $table->string('persona_id', 10);
            $table->foreign('persona_id')->references('carnet_identidad')->on('personas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
