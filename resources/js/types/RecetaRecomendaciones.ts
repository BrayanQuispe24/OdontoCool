import { DetalleRecomendacion } from "./DetalleRecomendacion";

export interface RecetaRecomendaciones {
    id: number;
    fecha_emision: string;
    observacion_general?: string;
    detalles: DetalleRecomendacion[];
}