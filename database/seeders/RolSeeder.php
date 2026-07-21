<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'description' => 'Rol con todos los permisos',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Paciente',
                'description' => 'Rol para pacientes',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Secretaria',
                'description' => 'Rol para secretarias',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Doctor',
                'description' => 'Rol para doctores',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Propietario',
                'description' => 'Rol para propietarios de clínicas',
                'estado' => 'activo',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}