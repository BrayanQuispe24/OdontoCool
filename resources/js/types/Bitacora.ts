export interface Persona {
    carnet_identidad: string;
    nombre: string;
    apellido: string;
    fecha_nacimiento?: string;
    direccion?: string;
    genero?: string;
    telefono?: string;
}

export interface Bitacora {
    id: number;
    persona_id?: string | null;
    accion: string;
    modulo: string;
    fecha: string;
    hora: string;
    ip_address?: string | null;
    user_agent?: string | null;
    detalles?: any | null;
    created_at?: string;
    updated_at?: string;
    persona?: Persona | null;
}
