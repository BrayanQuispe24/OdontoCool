<?php

namespace Database\Seeders;

use App\Models\Modulo;
use App\Models\Permiso;
use Illuminate\Database\Seeder;
use RuntimeException;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = Modulo::pluck('id', 'nombre');

        $permisosPorModulo = [
            'Roles' => [
                'rol.index',
                'rol.store',
                'rol.show',
                'rol.update',
                'rol.destroy',
            ],

            'Permisos' => [
                'permiso.index',
                'permiso.store',
                'permiso.show',
                'permiso.update',
                'permiso.destroy',
            ],

            'Modulos' => [
                'modulo.index',
                'modulo.store',
                'modulo.show',
                'modulo.update',
                'modulo.destroy',
            ],

            'Usuarios' => [
                'usuario.index',
                'usuario.store',
                'usuario.show',
                'usuario.update',
                'usuario.destroy',
            ],

            'Doctores' => [
                'doctor.index',
                'doctor.store',
                'doctor.show',
                'doctor.update',
                'doctor.destroy',
            ],

            'Pacientes' => [
                'paciente.index',
                'paciente.store',
                'paciente.show',
                'paciente.update',
                'paciente.destroy',
            ],

            'Secretarias' => [
                'secretaria.index',
                'secretaria.store',
                'secretaria.show',
                'secretaria.update',
                'secretaria.destroy',
            ],

            'Propietarios' => [
                'propietario.index',
                'propietario.store',
                'propietario.show',
                'propietario.update',
                'propietario.destroy',
            ],

            'Servicios' => [
                'servicio.index',
                'servicio.store',
                'servicio.show',
                'servicio.update',
                'servicio.destroy',
            ],

            'Citas' => [
                'citas.index',
                'citas.store',
                'citas.show',
                'citas.update',
                'citas.destroy',
            ],

            'Especialidades' => [
                'especialidad.index',
                'especialidad.store',
                'especialidad.show',
                'especialidad.update',
                'especialidad.destroy',
            ],

            'Tratamientos' => [
                'tratamiento.index',
                'tratamiento.store',
                'tratamiento.show',
                'tratamiento.update',
                'tratamiento.destroy',
            ],

            'Analisis' => [
                'analisis.index',
                'analisis.store',
                'analisis.show',
                'analisis.update',
                'analisis.destroy',
            ],

            'Historial Clinico' => [
                'historial_clinico.index',
                'historial_clinico.store',
                'historial_clinico.show',
                'historial_clinico.update',
                'historial_clinico.destroy',
            ],

            'Diagnosticos' => [
                'diagnostico.index',
                'diagnostico.store',
                'diagnostico.show',
                'diagnostico.update',
                'diagnostico.destroy',
            ],

            'Turnos' => [
                'turnos.index',
                'turnos.store',
                'turnos.show',
                'turnos.update',
                'turnos.destroy',
            ],

            'Boleta Pago' => [
                'boleta_pago.index',
                'boleta_pago.store',
                'boleta_pago.pagar',
                'boleta_pago.destroy',
                'boleta_pago.generar_qr',
                'boleta_pago.verificar_qr',
                'boleta_pago.callback',
            ],

            'Solicitud analisis' => [
                'solicitud_analisis.index',
                'solicitud_analisis.store',
                'solicitud_analisis.show',
                'solicitud_analisis.update',
                'solicitud_analisis.destroy',
            ],
            'Resultado analisis' => [
                'resultado_analisis.index',
                'resultado_analisis.store',
                'resultado_analisis.show',
                'resultado_analisis.update',
                'resultado_analisis.destroy',
            ],
            'Reportes' => [
                'reportes.index',
            ],

            'Bitacora' => [
                'bitacora.index',
                'bitacora.store',
            ],
        ];

        foreach ($permisosPorModulo as $nombreModulo => $permisos) {
            if (! isset($modulos[$nombreModulo])) {
                throw new RuntimeException("No existe el módulo: {$nombreModulo}");
            }

            foreach ($permisos as $nombrePermiso) {
                Permiso::updateOrCreate(
                    ['nombre' => $nombrePermiso],
                    [
                        'nombre' => $nombrePermiso,
                        'modulo_id' => $modulos[$nombreModulo],
                        'estado' => 'activo',
                    ]
                );
            }
        }
    }
}
