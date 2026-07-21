<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import FormError from '../Form/FormError.vue';
interface UsuarioPropietario { codigo_usuario?: string; email?: string; estado?: string; url?: string | null; rol?: { nombre?: string; } | null; }
defineProps<{ 
    usuario: UsuarioPropietario | null; 
    nombreCompleto: string; 
    iniciales: string; 
    previewFoto: string | null; 
    editando: boolean; 
    processing: boolean; 
    mostrarVolver: boolean; 
    errorFoto?: string; }>();
const emit = defineEmits<{ edit: []; cancel: []; save: []; 'photo-change': [file: File | null]; }>();
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
const assetUrl = (path: string | null | undefined,): string | undefined => {
    if (!path) { return undefined; } if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:')) { return path; }
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace(/^\/+/, ''); return `${base}/${cleanPath}`;
};
const seleccionarFoto = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null; emit('photo-change', file);
}; 
</script>
<template>
    <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="h-32 bg-gradient-to-r from-[var(--primary)] via-[#18dccf] to-[#bcefeb]" />
        <div class="relative p-6">
            <div class="-mt-20 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="relative">
                        <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl border-4 bg-[var(--card)] text-[length:var(--font-3xl)] font-black text-[var(--primary)] shadow-xl"
                            style="border-color: var(--card)"> <img v-if="previewFoto" :src="assetUrl(previewFoto)"
                                alt="Foto del propietario" class="h-full w-full object-cover" /> <span v-else> {{
                                    iniciales || 'AD' }} </span> </div> <label v-if="editando"
                            class="absolute -bottom-2 -right-2 cursor-pointer rounded-2xl bg-[var(--primary)] px-4 py-2 text-[length:var(--font-xs)] font-black text-white shadow-lg transition hover:opacity-90">
                            Foto <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
                                @change="seleccionarFoto" /> </label>
                    </div>
                    <div>
                        <p
                            class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Información del registro </p>
                        <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]"> {{ nombreCompleto
                            || 'Sin nombre registrado' }} </h1>
                        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]"> {{ usuario?.rol?.nombre ??
                            'Administrador' }} </p>
                        <p class="mt-1 break-words text-[length:var(--font-sm)] text-[var(--muted)]"> {{ usuario?.email
                            ?? 'Sin email registrado' }} </p>
                        <FormError :message="errorFoto" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link v-if="mostrarVolver" :href="route('administrador.index')"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--bg)]"
                        style="border-color: var(--border)"> Volver </Link> <button v-if="!editando" type="button"
                        class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90"
                        @click="emit('edit')"> Editar </button> <button v-if="editando" type="button"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--bg)]"
                        style="border-color: var(--border)" @click="emit('cancel')"> Cancelar </button> <button
                        v-if="editando" type="button" :disabled="processing"
                        class="rounded-2xl bg-[var(--primary)] px-5 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        @click="emit('save')"> {{ processing ? 'Guardando...' : 'Guardar cambios' }} </button>
                </div>
            </div>
        </div>
    </div>
</template>