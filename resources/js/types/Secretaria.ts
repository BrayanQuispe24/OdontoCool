import { Turno } from "./Turno";

export interface Secretaria {
    codigo_secretaria: string;
    persona_id: string;
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

export interface SecretariaShow {
    codigo_secretaria: string;
    persona_id: string;
    fecha_contratacion: string;
    turnos?: Turno[];
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
