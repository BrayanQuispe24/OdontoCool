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
        Schema::create('asignacion_estado_cita', function (Blueprint $table) {
            $table->id();
            $table->text('observacion')->default('Sin observación');
            $table->foreignId('cita_id')->constrained('citas', 'id_cita')->onDelete('cascade');
            $table->foreignId('estado_cita_id')->constrained('estado_cita', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_estado_cita');
    }
};
