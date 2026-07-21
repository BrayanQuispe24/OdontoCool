export interface Turno {
    id: number;
    nombre: string;
    estado: string;
    hora_inicio: string;
    hora_fin: string;
    pivot?: {
        id: number;
        doctor_id: string;
        turno_id: number;
        dia_semana: string;
    };
}

export interface AsignacionTurno {
    id: number;
    dia_semana: string;
    doctor_id: number;
    turno_id: number;
}