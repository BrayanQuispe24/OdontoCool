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
        Schema::table('resultado_analisis', function (Blueprint $table) {
            $table->renameColumn('archivo_adjunto', 'archivo_resultado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultado_analisis', function (Blueprint $table) {
            $table->renameColumn('archivo_resultado', 'archivo_adjunto');
        });
    }
};
