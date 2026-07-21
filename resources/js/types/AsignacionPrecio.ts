import { Precio } from "./Precio";

export interface AsignacionPrecio {
    id: number;
    servicio_id: number;
    precio_id: number;
    fecha_inicio: string;
    fecha_fin: string;
    estado: string;
    created_at?: string;
    updated_at?: string;
    precio?: Precio;
}
