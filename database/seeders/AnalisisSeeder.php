<?php

namespace Database\Seeders;

use App\Models\Analisis;
use Illuminate\Database\Seeder;

class AnalisisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $analisis = [
            [
                'nombre' => 'Radiografía Periapical',
                'descripcion' => 'Radiografía intraoral para evaluar uno o varios dientes y sus raíces.',
            ],
            [
                'nombre' => 'Radiografía Bite-Wing',
                'descripcion' => 'Permite detectar caries interproximales y evaluar el nivel óseo.',
            ],
            [
                'nombre' => 'Radiografía Oclusal',
                'descripcion' => 'Radiografía para evaluar el desarrollo dental y estructuras del maxilar.',
            ],
            [
                'nombre' => 'Radiografía Panorámica',
                'descripcion' => 'Visualiza toda la cavidad oral, maxilares y dientes en una sola imagen.',
            ],
            [
                'nombre' => 'Telerradiografía Lateral',
                'descripcion' => 'Estudio utilizado principalmente para planificación ortodóncica.',
            ],
            [
                'nombre' => 'Tomografía Cone Beam (CBCT)',
                'descripcion' => 'Tomografía tridimensional utilizada para implantes, cirugía y endodoncia.',
            ],
            [
                'nombre' => 'Fotografía Clínica Intraoral',
                'descripcion' => 'Registro fotográfico para diagnóstico y seguimiento del tratamiento.',
            ],
            [
                'nombre' => 'Modelos de Estudio',
                'descripcion' => 'Obtención de modelos dentales para análisis o planificación.',
            ],
            [
                'nombre' => 'Hemograma Completo',
                'descripcion' => 'Examen solicitado antes de procedimientos quirúrgicos cuando es necesario.',
            ],
            [
                'nombre' => 'Tiempo de Coagulación',
                'descripcion' => 'Evalúa la capacidad de coagulación del paciente antes de cirugía.',
            ],
            [
                'nombre' => 'Tiempo de Sangría',
                'descripcion' => 'Complementa la evaluación del riesgo de sangrado.',
            ],
            [
                'nombre' => 'Glucemia',
                'descripcion' => 'Control de niveles de glucosa en pacientes diabéticos o con sospecha clínica.',
            ],
            [
                'nombre' => 'Prueba de VIH',
                'descripcion' => 'Solicitada únicamente cuando el protocolo clínico lo requiere.',
            ],
            [
                'nombre' => 'Prueba de Hepatitis B',
                'descripcion' => 'Examen complementario para determinados procedimientos quirúrgicos.',
            ],
            [
                'nombre' => 'Prueba de Hepatitis C',
                'descripcion' => 'Examen complementario según criterio profesional.',
            ],
        ];

        foreach ($analisis as $item) {
            Analisis::updateOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'descripcion' => $item['descripcion'],
                    'estado' => 'ACTIVO',
                ]
            );
        }
    }
}