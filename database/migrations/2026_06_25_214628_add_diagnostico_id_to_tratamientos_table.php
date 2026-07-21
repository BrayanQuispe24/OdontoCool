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
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->foreignId('diagnostico_id')
                ->nullable()
                ->unique()
                ->after('fecha_fin_real')
                ->constrained('diagnosticos')
                ->onDelete('cascade');

            $table->foreignId('codigo_historial')
                ->nullable()
                ->after('diagnostico_id')
                ->constrained('historiales_clinicos', 'codigo_historial')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->dropForeign(['diagnostico_id']);
            $table->dropColumn('diagnostico_id');
            $table->dropForeign(['codigo_historial']);
            $table->dropColumn('codigo_historial');
        });
    }
};
