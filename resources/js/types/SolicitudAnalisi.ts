import { Analisis } from "./Analisis";
import { ResultadoAnalsis } from "./ResultadoAnalisis";
import { Tratamiento } from "./Tratamiento";
import { Doctor } from "./doctor";

export interface SolicitudAnalisis{
    id: number;
    fecha_solicitud: string;
    observacion?: string;
    motivo?: string;
    estado: string;
    analisis?: Analisis;
    resultado_analisis?: ResultadoAnalsis;
    tratamiento: Tratamiento;
    doctor_ci?: string;
    doctor?: Doctor;
}