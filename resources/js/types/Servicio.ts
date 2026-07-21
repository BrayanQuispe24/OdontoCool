import { AsignacionPrecio } from "./AsignacionPrecio";

export interface Servicio {
    id: number;
    nombre: string;
    descripcion: string;
    tipo: string;
    estado: string;
    created_at?: string;
    updated_at?: string;
    asignaciones_precio?: AsignacionPrecio[];
    asignaciones_precio_count?: number;
}
export interface ServicioWithAsignaciones extends Servicio {
    asignaciones_precio: AsignacionPrecio[];
}
