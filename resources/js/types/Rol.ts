import { Permiso } from "./Permiso";

export interface Rol{
    id:number,
    nombre:string,
    description:string,
    estado:string,
    created_at:string,
    updated_at:string,
}
export interface rolWithPermisos extends Rol{
    permisos: Permiso[];
}

