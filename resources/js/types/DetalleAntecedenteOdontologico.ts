export interface DetalleAntecedenteOdontologico {
    id_detalle_antecedente: number;
    nombre_tratamiento: string;
    descripcion: string | null;
    fecha_tratamiento: string;
    lugar_tratamiento: string;
    observacion: string | null;
    id_antecedente: number;
    created_at?: string;
    updated_at?: string;
}
