import { Cita } from './Cita';
import { DetalleDiagnostico } from './DetalleDiagnostico';
import { Tratamiento } from './Tratamiento';

export interface Diagnostico {
    id: number;
    sintomas: string;
    tipo_diagnostico: string;
    gravedad: string;
    observaciones: string | null;
    cita_id: number;
    
    cita?: Cita;
    detalle_diagnostico?: DetalleDiagnostico[];
    tratamiento?: Tratamiento;
    created_at?: string;
    updated_at?: string;
}
