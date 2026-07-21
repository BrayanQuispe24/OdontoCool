<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud_analisis', function (Blueprint $table) {
            $table->string('doctor_ci', 15)->nullable()->after('tratamiento_id');
            $table->foreign('doctor_ci')->references('codigo_doctor')->on('doctores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_analisis', function (Blueprint $table) {
            $table->dropForeign(['doctor_ci']);
            $table->dropColumn('doctor_ci');
        });
    }
};
