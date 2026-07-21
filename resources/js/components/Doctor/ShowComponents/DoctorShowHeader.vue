<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { DoctorShow } from '@/types/doctor';

const props = defineProps<{
    doctor: DoctorShow;
    editando: boolean;
    previewFoto: string | null;
    iniciales: string;
    nombreCompleto: string;
    usuario: any;
    tieneRolUser: boolean; // is logged-in user doctor
    tienePermisoUpdate: boolean;
    formProcessing: boolean;
}>();

const emit = defineEmits<{
    'change-foto': [event: Event];
    'activar-edicion': [];
    'cancelar-edicion': [];
    'guardar-cambios': [];
}>();

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const assetUrl = (path: string | null | undefined): string | undefined => {
    if (!path) return undefined;
    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('blob:')
    ) {
        return path;
    }
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace(/^\/+/, '');
    return `${base}/${cleanPath}`;
};
</script>

<template>
    <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <div class="h-32 bg-gradient-to-r from-[var(--primary)] via-[#18dccf] to-[#bcefeb]"></div>

        <div class="relative p-6">
            <div class="relative z-10 -mt-20 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="relative">
                        <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl border-4 bg-[var(--card)] shadow-xl border-[var(--card)]">
                            <img v-if="previewFoto" :src="assetUrl(previewFoto)" alt="Foto de perfil"
                                class="h-full w-full object-cover" />

                            <span v-else class="text-3xl font-black text-[var(--text)]">
                                {{ iniciales || 'DO' }}
                            </span>
                        </div>

                        <label v-if="editando"
                            class="absolute -bottom-2 -right-2 cursor-pointer rounded-2xl bg-[var(--primary)] px-4 py-2 text-[length:var(--font-xs)] font-black text-white shadow-lg transition hover:opacity-90 active:scale-95 duration-100">
                            Foto
                            <input type="file" accept="image/*" class="hidden" @change="emit('change-foto', $event)" />
                        </label>
                    </div>

                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Información del registro
                        </p>

                        <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                            {{ nombreCompleto || 'Sin nombre registrado' }}
                        </h1>

                        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                            {{ usuario?.rol?.nombre ?? 'Doctor' }}
                        </p>

                        <p class="mt-1 break-words text-[length:var(--font-sm)] text-[var(--muted)]">
                            {{ usuario?.email ?? 'Sin email registrado' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link v-if="!tieneRolUser" :href="route('doctor.index')"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--bg)] border-[var(--border)]">
                        Volver
                    </Link>

                    <button v-if="!editando && tienePermisoUpdate" type="button"
                        class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90"
                        @click="emit('activar-edicion')">
                        Editar
                    </button>

                    <button v-if="editando && tienePermisoUpdate" type="button"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--bg)] border-[var(--border)]"
                        @click="emit('cancelar-edicion')">
                        Cancelar
                    </button>

                    <button v-if="editando && tienePermisoUpdate" type="button"
                        :disabled="formProcessing"
                        class="rounded-2xl bg-[var(--primary)] px-5 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        @click="emit('guardar-cambios')">
                        {{ formProcessing ? 'Guardando...' : 'Guardar cambios' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
