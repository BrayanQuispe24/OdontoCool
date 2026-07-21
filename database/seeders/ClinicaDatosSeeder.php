<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HistorialClinico;
use App\Models\Cita;
use App\Models\Diagnostico;
use App\Models\Diente;

class ClinicaDatosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed clinical histories for patients
        $historial1 = HistorialClinico::updateOrCreate(
            ['paciente_ci' => 'PAC001'],
            [
                'alergias' => 'Penicilina',
                'antecedentes_medicos' => 'Operación de apéndice en 2021',
                'enfermedades_base' => 'Hipertensión controlada',
                'motivo_apertura' => 'Limpieza general y dolor en molar inferior',
                'fecha_apertura' => '2026-06-01',
                'observaciones_generales' => 'Paciente colaborador, sensible a la anestesia',
                'estado' => 'ACTIVO',
            ]
        );

        $historial2 = HistorialClinico::updateOrCreate(
            ['paciente_ci' => 'PAC002'],
            [
                'alergias' => 'Ninguna',
                'antecedentes_medicos' => 'Tratamiento de ortodoncia previo',
                'enfermedades_base' => 'Ninguna',
                'motivo_apertura' => 'Sensibilidad al frío extrema',
                'fecha_apertura' => '2026-06-05',
                'observaciones_generales' => 'Higiene bucal regular, requiere motivación',
                'estado' => 'ACTIVO',
            ]
        );

        $historial3 = HistorialClinico::updateOrCreate(
            ['paciente_ci' => 'PAC003'],
            [
                'alergias' => 'Látex',
                'antecedentes_medicos' => 'Asma estacional',
                'enfermedades_base' => 'Asma',
                'motivo_apertura' => 'Sangrado de encías frecuente',
                'fecha_apertura' => '2026-06-10',
                'observaciones_generales' => 'Llevar inhalador a consulta siempre',
                'estado' => 'ACTIVO',
            ]
        );

        // 2. Seed appointments (citas)
        // Cita 1: Completed & Diagnosed (will be associated with a diagnosis below)
        $cita1 = Cita::updateOrCreate(
            [
                'paciente_ci' => 'PAC001',
                'fecha_cita' => '2026-06-25',
                'hora_inicio' => '09:00:00',
            ],
            [
                'hora_fin' => '09:30:00',
                'motivo' => 'Revisión por dolor agudo',
                'observacion' => 'Paciente refiere dolor constante en zona posterior izquierda',
                'codigo_historial' => $historial1->codigo_historial,
                'secretaria_ci' => 'SEC001',
                'doctor_ci' => 'DOC001',
            ]
        );

        // Cita 2: Available (no diagnosis yet, to test creating a diagnosis!)
        $cita2 = Cita::updateOrCreate(
            [
                'paciente_ci' => 'PAC002',
                'fecha_cita' => '2026-06-27',
                'hora_inicio' => '10:00:00',
            ],
            [
                'hora_fin' => '10:30:00',
                'motivo' => 'Control de caries y sensibilidad',
                'observacion' => 'Revisar cuadrante superior derecho',
                'codigo_historial' => $historial2->codigo_historial,
                'secretaria_ci' => 'SEC001',
                'doctor_ci' => 'DOC001',
            ]
        );

        // Cita 3: Available (no diagnosis yet, to test creating a diagnosis!)
        $cita3 = Cita::updateOrCreate(
            [
                'paciente_ci' => 'PAC003',
                'fecha_cita' => '2026-06-28',
                'hora_inicio' => '14:00:00',
            ],
            [
                'hora_fin' => '14:30:00',
                'motivo' => 'Gingivitis y profilaxis',
                'observacion' => 'Limpieza dental profunda planificada',
                'codigo_historial' => $historial3->codigo_historial,
                'secretaria_ci' => 'SEC002',
                'doctor_ci' => 'DOC002',
            ]
        );

        // 3. Seed an initial Diagnosis with details
        $diagnostico = Diagnostico::updateOrCreate(
            ['cita_id' => $cita1->id_cita],
            [
                'sintomas' => 'Dolor agudo a la masticación y percusión vertical',
                'tipo_diagnostico' => 'Pulpitis irreversible aguda',
                'gravedad' => 'SEVERA',
                'observaciones' => 'Se sugiere tratamiento de conducto de manera prioritaria.',
            ]
        );

        // Retrieve seeded teeth (e.g. molar #36 or similar, or first tooth in DB)
        $diente = Diente::where('numero', 36)->first() ?? Diente::first();

        if ($diente) {
            $diagnostico->detalleDiagnostico()->updateOrCreate(
                [
                    'diagnostico_id' => $diagnostico->id,
                    'diente_id' => $diente->id,
                ],
                [
                    'zona_bucal' => 'Oclusal',
                    'observacion' => 'Caries penetrante clase I que compromete la cámara pulpar.',
                ]
            );
        }
    }
}
