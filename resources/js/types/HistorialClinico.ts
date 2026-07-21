import { Paciente } from './Paciente';
import { Cita } from './Cita';
import { AntecedentesOdontologicos } from './AntecedentesOdontologicos';
import { Tratamiento } from './Tratamiento';

export interface HistorialClinico {
    codigo_historial: number;
    alergias: string | null;
    antecedentes_medicos: string | null;
    enfermedades_base: string | null;
    motivo_apertura: string;
    fecha_apertura: string;
    fecha_actualizacion: string | null;
    observaciones_generales: string | null;
    estado: 'ACTIVO' | 'INACTIVO';
    paciente_ci: string;
    
    paciente?: Paciente;
    citas?: Cita[];
    antecedentes_odontologicos?: AntecedentesOdontologicos;
    tratamiento?: Tratamiento[];
    created_at?: string;
    updated_at?: string;
}
