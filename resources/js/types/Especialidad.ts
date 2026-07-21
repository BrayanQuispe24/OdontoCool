export interface Especialidad {
    id: number;
    nombre: string;
    descripcion?: string;
    estado: 'activo' | 'inactivo' | 'suspendido';
    created_at: string;
    updated_at: string;
}

export interface AsignacionEspecialidad {
    id: number;
    doctor_id: string;
    especialidad_id: number;
    created_at: string;
    updated_at: string;
}