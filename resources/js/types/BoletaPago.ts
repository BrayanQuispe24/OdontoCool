import { ModoPago } from "./ModoPago";
import { Paciente } from "./Paciente";
import { Secretaria } from "./Secretaria";
import { ServicioPrestado } from "./ServicioPrestado";
import { CuotaBoleta } from "./CuotaBoleta";

export interface BoletaPago {
    id_boleta: number;
    descuento: number;
    fecha_emision: string;
    total: number;
    estado_pago: 'PENDIENTE' | 'PARCIAL' | 'PAGADO' | 'ELIMINADO';
    id_modo_pago: number;
    paciente_ci: string;
    secretaria_ci?: string;
    modo_pago?: ModoPago;
    paciente?: Paciente;
    secretaria?: Secretaria;
    servicio_prestado?: ServicioPrestado[];
    cuota_boleta?: CuotaBoleta[];
    created_at?: string;
    updated_at?: string;
}
