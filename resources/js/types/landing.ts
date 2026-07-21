export type LandingResultType = 'Servicio' | 'Análisis' | 'Doctor' | 'Información';

export type LandingService = {
    id: number;
    title: string;
    description: string;
    badge: string;
    anchor: string;
};

export type LandingAnalysis = {
    id: number;
    title: string;
    description: string;
    badge: string;
    anchor: string;
};

export type LandingDoctor = {
    codigo_doctor: string;
    title: string;
    description: string;
    anchor: string;
    photo: string | null;
    telefono: string | null;
    correo: string | null;
    especialidades: string[];
    informacion_adicional: string[];
    turnos: string[];
};

export type LandingClinicCard = {
    type: 'Información';
    title: string;
    description: string;
    anchor: string;
    badge: string;
};

export type LandingClinic = {
    nombre: string;
    descripcion: string;
    resumen: {
        servicios: number;
        analisis: number;
        doctores: number;
        especialidades: number;
    };
    cards: LandingClinicCard[];
};

export type LandingSearchResult = {
    type: LandingResultType;
    title: string;
    description: string;
    anchor: string;
    section: 'servicios' | 'analisis' | 'doctores' | 'clinica';
    badge?: string | null;
};

export interface LandingPageProps {
    servicios: LandingService[];
    analisis: LandingAnalysis[];
    doctores: LandingDoctor[];
    clinica: LandingClinic;
}
