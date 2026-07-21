import { DetalleAntecedenteOdontologico } from './DetalleAntecedenteOdontologico';

export interface AntecedentesOdontologicos {
    id_antecedente: number;
    observacion_general: string | null;
    fecha_registro: string;
    codigo_historial: number;
    detalle_antecedentes?: DetalleAntecedenteOdontologico[];
    created_at?: string;
    updated_at?: string;
}
