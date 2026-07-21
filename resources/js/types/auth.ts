import { PageProps as InertiaPageProps } from '@inertiajs/core';

export type Theme = 'light' | 'dark' | 'odontocool';

export type FontSize = 'small' | 'normal' | 'large' | 'extra-large';

export type Rol = {
    id: number;
    nombre: string;
    description?: string | null;
    estado?: string;
};

export type Permiso = {
    id: number;
    nombre: string;
    estado?: string;
    modulo_id?: number;
};

export type Modulo = {
    id: number;
    nombre: string;
    estado?: string;
    permisos?: Permiso[];
};

export type User = {
    id: number | string;
    codigo_usuario?: string | null;
    persona_id?: string | null;

    name: string;
    nombre?: string | null;
    apellido?: string | null;
    nombre_completo?: string | null;

    email: string;
    url?: string | null;
    email_verified_at?: string | null;
    created_at?: string;
    updated_at?: string;

    rol?: Rol | null;

    preferencias?: {
        theme: Theme;
        font_size: FontSize;
    };

    [key: string]: unknown;
};

export type Auth = {
    user: User | null;
    modulos: Modulo[];
    permisos: string[];
};

export interface PageProps extends InertiaPageProps {
    auth: Auth;

    flash?: {
        success?: string;
        error?: string;
        warning?: string;
        flash_id?: string;
    };
}