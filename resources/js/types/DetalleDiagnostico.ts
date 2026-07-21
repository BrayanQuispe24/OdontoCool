import { Diente } from './Diente';

export interface DetalleDiagnostico {
    id: number;
    observacion: string | null;
    zona_bucal: string;
    diagnostico_id: number;
    diente_id: number;
    diente?: Diente;
    created_at?: string;
    updated_at?: string;
}
