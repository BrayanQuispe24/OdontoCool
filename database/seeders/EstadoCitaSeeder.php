<?php

namespace Database\Seeders;

use App\Models\EstadoCita;
use Illuminate\Database\Seeder;

class EstadoCitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $estados = [
            ['nombre' => 'Pendiente', 'descripcion' => 'Cita pendiente de confirmación'],
            ['nombre' => 'Confirmado', 'descripcion' => 'Cita confirmada'],
            ['nombre' => 'Cancelado', 'descripcion' => 'Cita cancelada'],
            ['nombre' => 'Reprogramado', 'descripcion' => 'Cita reprogramada'],
            ['nombre' => 'Completado', 'descripcion' => 'Cita completada'],
        ];

        foreach ($estados as $estado) {
            EstadoCita::updateOrCreate(
                ['nombre' => $estado['nombre'],
                    'descripcion' => $estado['descripcion']],
                $estado
            );
        }
    }
}
