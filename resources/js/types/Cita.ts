import { Diagnostico } from './Diagnostico';
import { Turno } from './Turno';

// resources/js/types/Cita.ts

export interface Cita {
    id_cita: number;
    fecha_cita: string;
    hora_inicio: string;
    hora_fin: string;
    motivo: string;
    observacion: string | null;

    paciente_ci: string;
    codigo_historial: number | null;
    secretaria_ci: string | null;
    doctor_ci: string;

    // Si esta columna no existe realmente en tu tabla citas,
    // puedes quitarla. El estado viene desde ultimo_estado_asignado.
    estado_cita_id?: number | null;

    paciente?: Paciente | null;
    secretaria?: Secretaria | null;
    doctor?: Doctor | null;

    diagnostico?: Diagnostico | null;

    // Laravel convierte ultimoEstadoAsignado a ultimo_estado_asignado
    ultimo_estado_asignado?: AsignacionEstadoCita | null;

    // Laravel convierte asignacionesEstadoCita a asignaciones_estado_cita
    asignaciones_estado_cita?: AsignacionEstadoCita[];
}

export interface Paciente {
    codigo_paciente: string;
    persona_id: string | number;
    contacto_emergencia: string | null;
    telefono_emergencia: string | null;

    persona?: Persona | null;
}

export interface Secretaria {
    codigo_secretaria: string;
    persona_id: string | number;
    fecha_contratacion: string | null;

    persona?: Persona | null;
}

export interface Doctor {
    codigo_doctor: string;
    persona_id: string | number;
    matricula_profesional: string | null;
    experiencia: number | null;
    telefono_profesional: string | null;
    fecha_contratacion: string | null;

    persona?: Persona | null;
    turnos?: Turno[] | null;
}

export interface Persona {
    carnet_identidad: string;
    nombre: string;
    apellido: string;
    telefono: string | null;
    fecha_nacimiento: string | null;
    direccion: string | null;
    genero: string | null;
}

export interface AsignacionEstadoCita {
    id?: number;
    cita_id?: number;
    estado_cita_id?: number;
    observacion?: string | null;
    fecha_asignacion?: string | null;
    created_at?: string | null;
    updated_at?: string | null;

    estado_cita?: EstadoCita | null;
}

export interface EstadoCita {
    id: number;
    nombre: string;
}