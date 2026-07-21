import { Especialidad } from "./Especialidad";
import { Turno } from "./Turno";

export interface Doctor {
    codigo_doctor: string;
    persona_id: string;
    matricula_profesional: string;
    experiencia: number;
    telefono_profesional: string;
    fecha_contratacion: string;
    persona?: {
        carnet_identidad: string;
        nombre: string;
        apellido: string;
        telefono: string;
        fecha_nacimiento?: string;
        direccion?: string;
        genero?: string;
        usuarios?: {
            codigo_usuario: string;
            email: string;
            url?: string;
            estado?: 'activo' | 'inactivo';
            rol?: {
                id: number;
                nombre: string;
            };
        }[];
    }
}

export interface DoctorShow {
    codigo_doctor: string;
    persona_id: string;
    matricula_profesional: string;
    experiencia: number;
    telefono_profesional: string;
    fecha_contratacion: string;
    especialidades?: Especialidad[];
    turnos?:Turno[];

    persona?: {
        carnet_identidad: string;
        nombre: string;
        apellido: string;
        telefono: string;
        fecha_nacimiento: string;
        direccion: string | null;
        genero: string;
        usuarios?: {
            codigo_usuario: string;
            estado: 'activo' | 'inactivo';
            url: string | null;
            email: string;
            persona_id: string;
            rol_id: number;
            rol?: {
                id: number;
                nombre: string;
            };
        }[];
    }
}
