export interface Paciente {
    codigo_paciente: string;
    persona_id: string;
    contacto_emergencia: string;
    telefono_emergencia: string;
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

export interface PacienteShow {
    codigo_paciente: string;
    persona_id: string;
    contacto_emergencia: string;
    telefono_emergencia: string;

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
