<?php

namespace Database\Seeders;

// use App\Models\Propietario;

use App\Models\Propietario;
use Illuminate\Database\Seeder;

class PropietarioSeeder extends Seeder
{
    public function run(): void
    {
        $propietarios = [
            ['codigo_propietario' => 'PRO001', 'fecha_inicio' => '2021-01-01', 'porcentaje_participacion' => '60', 'persona_id' => '1000009'],
            // ['codigo_propietario' => 'PRO002', 'fecha_inicio' => '2021-01-01', 'porcentaje_participacion' => '40', 'persona_id' => '1000005'],
        ];

        foreach ($propietarios as $propietario) {
            Propietario::updateOrCreate(
                ['codigo_propietario' => $propietario['codigo_propietario']],
                $propietario
            );
        }
    }
}