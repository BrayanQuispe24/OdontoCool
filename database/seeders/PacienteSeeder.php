<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            ['codigo_paciente' => 'PAC001', 'contacto_emergencia' => 'Ana Rojas', 'telefono_emergencia' => '71000001', 'persona_id' => '1000001'],
            ['codigo_paciente' => 'PAC002', 'contacto_emergencia' => 'Luis Lopez', 'telefono_emergencia' => '71000002', 'persona_id' => '1000002'],
            ['codigo_paciente' => 'PAC003', 'contacto_emergencia' => 'Maria Vargas', 'telefono_emergencia' => '71000003', 'persona_id' => '1000003'],
            ['codigo_paciente' => 'PAC004', 'contacto_emergencia' => 'Pedro Mendoza', 'telefono_emergencia' => '71000004', 'persona_id' => '1000004'],
            ['codigo_paciente' => 'PAC005', 'contacto_emergencia' => 'Lucia Suarez', 'telefono_emergencia' => '71000005', 'persona_id' => '1000005'],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::updateOrCreate(
                ['codigo_paciente' => $paciente['codigo_paciente']],
                $paciente
            );
        }
    }
}