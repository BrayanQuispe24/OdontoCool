import { MetodoPago } from "./MetodoPago";
import { CuotaMulta } from "./CuotaMulta";

export interface CuotaBoleta {
    id_cuota: number;
    id_boleta: number;
    numero_cuota: number;
    monto_cuota: number;
    fecha_vencimiento: string;
    fecha_pago?: string;
    estado: 'PENDIENTE' | 'PAGADO' | 'ELIMINADO' | string;
    comprobante?: string;
    id_metodo_pago?: number;
    id_transaccion?: string;
    metodo_pago?: MetodoPago;
    cuota_multa?: CuotaMulta;
    created_at?: string;
    updated_at?: string;
}
