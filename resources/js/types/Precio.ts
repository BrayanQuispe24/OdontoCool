export interface Precio {
    id: number;
    moneda: string;
    monto: number | string;
    estado: string;
    created_at?: string;
    updated_at?: string;
}
