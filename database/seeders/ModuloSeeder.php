<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = [
            ['nombre' => 'Roles', 'estado' => 'activo'],
            ['nombre' => 'Permisos', 'estado' => 'activo'],
            ['nombre' => 'Modulos', 'estado' => 'activo'],
            ['nombre' => 'Usuarios', 'estado' => 'activo'],
            ['nombre' => 'Doctores', 'estado' => 'activo'],
            ['nombre' => 'Pacientes', 'estado' => 'activo'],
            ['nombre' => 'Secretarias', 'estado' => 'activo'],
            ['nombre' => 'Propietarios', 'estado' => 'activo'],
            ['nombre' => 'Servicios', 'estado' => 'activo'],
            ['nombre' => 'Citas', 'estado' => 'activo'],
            ['nombre' => 'Especialidades', 'estado' => 'activo'],
            ['nombre' => 'Tratamientos', 'estado' => 'activo'],
            ['nombre' => 'Analisis', 'estado' => 'activo'],
            ['nombre' => 'Historial Clinico', 'estado' => 'activo'],
            ['nombre' => 'Diagnosticos', 'estado' => 'activo'],
            ['nombre' => 'Turnos', 'estado' => 'activo'],
            ['nombre' => 'Boleta Pago', 'estado' => 'activo'],
            ['nombre'=> 'Solicitud analisis', 'estado' => 'activo'],
            ['nombre'=> 'Resultado analisis', 'estado' => 'activo'],
            ['nombre' => 'Bitacora', 'estado' => 'activo'],
            ['nombre'=> 'Reportes','estado'=>'activo'],
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(
                ['nombre' => $modulo['nombre']],
                $modulo
            );
        }
    }
}