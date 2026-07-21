export interface Propietario {
    codigo_propietario: string;
    persona_id: string;
    fecha_inicio: string;
    porcentaje_participacion: string;
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

export interface PropietarioRegistro {
    email: string;
    url: string;
    password: string;
    carnet_identidad: string;
    nombre: string;
    apellido: string;
    fecha_nacimiento: string;
    direccion: string;
    genero: string;
    telefono: string;
    fecha_inicio: string;
    porcentaje_participacion: string;
    estado: 'activo';
};
export interface PropietarioActualizacion {
    codigo_propietario: string;
    persona_id?: string;
    email: string;
    url: string;
    password: string;
    password_confirmation: string;
    carnet_identidad: string;
    nombre: string;
    apellido: string;
    fecha_nacimiento: string;
    direccion: string;
    genero: string;
    telefono: string;
    fecha_inicio: string;
    porcentaje_participacion: string;
    estado: 'activo';
};

export interface AdministradorShow {
    codigo_propietario: string;
    persona_id: string;
    fecha_inicio: string | null;
    porcentaje_participacion: string | number | null;

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
    };

}