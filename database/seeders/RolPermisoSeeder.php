<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;
use RuntimeException;

class RolPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdministrador = $this->obtenerRol('Administrador');
        $rolPaciente = $this->obtenerRol('Paciente');
        $rolSecretaria = $this->obtenerRol('Secretaria');
        $rolDoctor = $this->obtenerRol('Doctor');
        $rolPropietario = $this->obtenerRol('Propietario');

        /**
         * ADMINISTRADOR
         * Tiene todos los permisos registrados en la tabla permisos.
         */
        $rolAdministrador->permisos()->syncWithoutDetaching(
            Permiso::pluck('id')->toArray()
        );

        /**
         * PACIENTE
         */
        $this->asignarPermisos($rolPaciente, [
            'citas.index',
            'citas.store',
            'citas.update',
            'citas.show',

            'servicio.index',
            'servicio.show',

            'historial_clinico.show',

            'analisis.index',

            'paciente.show',
            'paciente.update',
            'paciente.store',

            'boleta_pago.index',
            'boleta_pago.pagar',
            'boleta_pago.generar_qr',
            'boleta_pago.verificar_qr',
            'boleta_pago.callback',
        ]);

        /**
         * SECRETARIA
         */
        $this->asignarPermisos($rolSecretaria, [
            'doctor.index',
            'doctor.show',

            'analisis.index',

            'secretaria.show',
            'secretaria.update',

            'paciente.index',
            'paciente.store',
            'paciente.show',
            'paciente.update',

            'servicio.index',
            'servicio.show',

            'citas.index',
            'citas.store',
            'citas.show',
            'citas.update',
            'citas.destroy',

            'analisis.index',

            'tratamiento.index',
            'tratamiento.show',
            'tratamiento.update',

            'boleta_pago.index',
            'boleta_pago.store',
            'boleta_pago.pagar',
            'boleta_pago.destroy',
            'boleta_pago.generar_qr',
            'boleta_pago.verificar_qr',
            'boleta_pago.callback',

        ]);

        /**
         * DOCTOR
         */
        $this->asignarPermisos($rolDoctor, [
            'doctor.show',
            'doctor.update',

            'paciente.index',
            'paciente.store',
            'paciente.show',
            'paciente.update',

            'secretaria.index',
            'secretaria.show',

            'servicio.index',
            'servicio.show',

            'citas.index',
            'citas.show',

            'tratamiento.index',
            'tratamiento.store',
            'tratamiento.show',
            'tratamiento.update',

            'analisis.index',
            'analisis.show',

            'solicitud_analisis.index',
            'solicitud_analisis.store',
            'solicitud_analisis.update',
            'solicitud_analisis.show',
            'solicitud_analisis.destroy',

            'resultado_analisis.index',
            'resultado_analisis.store',
            'resultado_analisis.show',

            'historial_clinico.index',
            'historial_clinico.store',
            'historial_clinico.show',
            'historial_clinico.update',

            'diagnostico.index',
            'diagnostico.store',
            'diagnostico.show',
            'diagnostico.update',
        ]);

        /**
         * PROPIETARIO
         */
        $this->asignarPermisos($rolPropietario, [
            'propietario.show',
            'propietario.update',

            'doctor.index',
            'doctor.show',
            'doctor.update',
            'doctor.store',
            'doctor.destroy',

            'paciente.index',

            'secretaria.index',
            'secretaria.show',
            'secretaria.update',
            'secretaria.store',
            'secretaria.destroy',

            'servicio.index',
            'servicio.show',
            'servicio.update',
            'servicio.store',
            'servicio.destroy',

            'especialidad.index',
            'especialidad.show',
            'especialidad.update',
            'especialidad.store',
            'especialidad.destroy',

            'turnos.index',
            'turnos.show',
            'turnos.update',
            'turnos.store',
            'turnos.destroy',

            'analisis.index',
            'analisis.show',
            'analisis.update',
            'analisis.store',
            'analisis.destroy',

            'reportes.index',
            'bitacora.index',

        ]);
    }

    private function obtenerRol(string $nombre): Rol
    {
        $rol = Rol::where('nombre', $nombre)->first();

        if (! $rol) {
            throw new RuntimeException("No existe el rol: {$nombre}");
        }

        return $rol;
    }

    private function asignarPermisos(Rol $rol, array $nombresPermisos): void
    {
        $permisos = Permiso::whereIn('nombre', $nombresPermisos)->get();

        $permisosEncontrados = $permisos->pluck('nombre')->toArray();

        $permisosFaltantes = array_diff($nombresPermisos, $permisosEncontrados);

        if (! empty($permisosFaltantes)) {
            throw new RuntimeException(
                'No existen los siguientes permisos: ' . implode(', ', $permisosFaltantes)
            );
        }

        $rol->permisos()->syncWithoutDetaching(
            $permisos->pluck('id')->toArray()
        );
    }
}