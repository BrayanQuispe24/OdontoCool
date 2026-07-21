import { TratamientoDiente } from './TratamientoDiente';
import { HistorialClinico } from './HistorialClinico';
import { Diagnostico } from './Diagnostico';
import { RecetaRecomendaciones } from './RecetaRecomendaciones';

export interface Tratamiento {
    id: number;
    objetivo_tratamiento: string;
    observacion?: string;
    estado: string;
    fecha_inicio: string;
    fecha_fin_estimada: string;
    fecha_fin_real?: string;
    codigo_historial?: number;
    historial_clinico?: HistorialClinico;
    diagnostico?:Diagnostico;
    receta_recomendaciones?: RecetaRecomendaciones;
    tratamientos_dientes?: TratamientoDiente[];
    created_at?: string;
    updated_at?: string;
}
