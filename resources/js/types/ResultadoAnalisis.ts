export interface ResultadoAnalsis {
    id: number;
    fecha_resultado: string;
    resultado: string;
    observaciones?: string;
    interpretacion?: string;
    archivo_resultado?: string;
    estado: string;
}