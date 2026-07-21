import { Diente } from './Diente';

export interface TratamientoDiente {
    id?: number;
    cara_dental: string;
    observacion?: string;
    fecha_registro?: string;
    tratamiento_planificado: string;
    estado?: string;
    diente_id: number;
    tratamiento_id?: number;
    diente?: Diente;
    created_at?: string;
    updated_at?: string;
}
