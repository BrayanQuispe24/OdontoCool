<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctores = [
            ['codigo_doctor' => 'DOC001', 'matricula_profesional' => 'MAT001', 'experiencia' => 5, 'telefono_profesional' => '72000001', 'fecha_contratacion' => '2023-03-01', 'persona_id' => '1000008'],
            ['codigo_doctor' => 'DOC002', 'matricula_profesional' => 'MAT002', 'experiencia' => 8, 'telefono_profesional' => '72000002', 'fecha_contratacion' => '2022-06-12', 'persona_id' => '1000009'],
        ];

        foreach ($doctores as $doctor) {
            Doctor::updateOrCreate(
                ['codigo_doctor' => $doctor['codigo_doctor']],
                $doctor
            );
        }
    }
}