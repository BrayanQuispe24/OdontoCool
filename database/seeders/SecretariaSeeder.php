<?php

namespace Database\Seeders;

use App\Models\Secretaria;
use Illuminate\Database\Seeder;

class SecretariaSeeder extends Seeder
{
    public function run(): void
    {
        $secretarias = [
            ['codigo_secretaria' => 'SEC001', 'fecha_contratacion' => '2024-01-10', 'persona_id' => '1000006'],
            ['codigo_secretaria' => 'SEC002', 'fecha_contratacion' => '2024-02-15', 'persona_id' => '1000007'],
        ];

        foreach ($secretarias as $secretaria) {
            Secretaria::updateOrCreate(
                ['codigo_secretaria' => $secretaria['codigo_secretaria']],
                $secretaria
            );
        }
    }
}