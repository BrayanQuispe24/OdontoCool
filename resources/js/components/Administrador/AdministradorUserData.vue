<script setup lang="ts">
import BaseInput from '../Form/BaseInput.vue';
import BaseSelect from '../Form/BaseSelect.vue';
import FormError from '../Form/FormError.vue';



interface UsuarioPropietario {
    codigo_usuario?: string;
    email?: string;
    estado?: string;
    url?: string | null;
    rol?: { nombre?: string; } | null;
}

defineProps<{
    email: string;
    estado: string;
    usuario: UsuarioPropietario | null;
    url: string | null;
    previewFoto: string | null;
    iniciales: string;
    editando: boolean;
    errors: Record<string, string | undefined>;
}>();

const emit = defineEmits<{
    'update:email': [value: string];
    'update:estado': [value: string];
    'photo-change': [file: File | null];
}>();

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
const assetUrl = (path: string | null | undefined,): string | undefined => {
    if (!path) { return undefined; } if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:')) { return path; }
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace(/^\/+/, ''); return `${base}/${cleanPath}`;
};
const seleccionarFoto = (event: Event) => { const input = event.target as HTMLInputElement; const file = input.files?.[0] ?? null; emit('photo-change', file); };
</script>
<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300"
        style="border-color: var(--border)">
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]"> Datos de
            usuario </p>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <BaseInput :model-value="email" name="email" label="Email" type="email" :disabled="!editando"
                :error="errors.email" class="sm:col-span-2"
                @update:model-value=" emit('update:email', String($event))" />
            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]"> Rol </p>
                <p class="mt-2 text-[length:var(--font-lg)] font-black text-[var(--title)]"> {{ usuario?.rol?.nombre ??
                    'Sin rol' }} </p>
            </div>
            <BaseSelect v-if="editando" :model-value="estado" name="estado" label="Estado"
                :options="[{ value: 'activo', label: 'Activo', }, { value: 'inactivo', label: 'Inactivo', },]"
                :error="errors.estado" @update:model-value=" emit('update:estado', String($event))" />
            <div v-else>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]"> Estado </p> <span
                    class="mt-3 inline-flex rounded-full px-4 py-2 text-[length:var(--font-sm)] font-black capitalize"
                    :class="estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"> {{
                        estado || 'Sin estado' }} </span>
            </div>
            <div class="sm:col-span-2">
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]"> URL de foto actual </p> <a
                    v-if="url" :href="assetUrl(url)" target="_blank" rel="noreferrer"
                    class="mt-2 block break-all rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:underline"
                    style="border-color: var(--border)"> {{ assetUrl(url) }} </a>
                <p v-else
                    class="mt-2 rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--muted)]"
                    style="border-color: var(--border)"> Sin URL registrada </p>
                <FormError :message="errors.url" />
            </div>
            <div v-if="editando" class="rounded-3xl border border-dashed p-5 text-center transition sm:col-span-2"
                style="border-color: var(--border)">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl bg-[var(--bg)] text-[length:var(--font-2xl)] font-black text-[var(--primary)]">
                    <img v-if="previewFoto" :src="assetUrl(previewFoto)" alt="Foto seleccionada"
                        class="h-full w-full object-cover" /> <span v-else> {{ iniciales || 'AD' }} </span>
                </div>
                <p class="mt-4 text-[length:var(--font-sm)] font-black text-[var(--title)]"> Subir nueva foto de perfil
                </p>
                <p class="mt-2 text-[length:var(--font-xs)] text-[var(--muted)]"> Formatos JPG, PNG o WEBP. Máximo 2 MB.
                </p> <label
                    class="mt-4 inline-flex cursor-pointer rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90">
                    Seleccionar imagen <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
                        @change="seleccionarFoto" /> </label>
                <FormError :message="errors.foto_perfil" />
            </div>
        </div>
    </div>
</template>