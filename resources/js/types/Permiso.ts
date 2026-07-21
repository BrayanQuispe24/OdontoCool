import { Modulo } from "./Modulo";

export interface Permiso {
    id:number,
    nombre: string,
    estado: string,
    modulo_id: number,
    modulo?: Modulo;
}