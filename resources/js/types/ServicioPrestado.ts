import { Servicio } from "./Servicio";

export interface ServicioPrestado {
    id: number;
    cantidad: number;
    precio: number;
    descuento: number;
    subtotal: number;
    fecha_servicio: string;
    observacion?: string;
    estado: string;
    tratamiento_id: number;
    servicio_id: number;
    created_at?: string;
    updated_at?: string;
    servicio?: Servicio;
}
