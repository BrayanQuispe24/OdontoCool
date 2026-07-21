<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PacienteShow } from '@/types/Paciente';

defineProps<{
    paciente: PacienteShow;
    editando: boolean;
    previewFoto: string | null;
    iniciales: string;
    tieneRol: (rol: string) => boolean;
    assetUrl: (path: string | null | undefined) => string | undefined;
    processing: boolean;
    fotoError?: string;
}>();

const emit = defineEmits<{
    'toggle-edit': [];
    'cancel-edit': [];
    'save': [];
    'change-photo': [event: Event];
}>();
</script>

<template>
    <!-- ENCABEZADO -->
    <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <div class="h-32 bg-gradient-to-r from-[var(--primary)] via-[#18dccf] to-[#bcefeb]"></div>

        <div class="relative p-6">
            <div class="-mt-20 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <!-- FOTO -->
                    <div class="relative">
                        <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl border-4 bg-[var(--card)] shadow-xl border-[var(--card)] shrink-0">
                            <img v-if="previewFoto" :src="assetUrl(previewFoto)" alt="Foto de perfil"
                                class="h-full w-full object-cover" />

                            <span v-else class="text-3xl font-black text-[var(--primary)]">
                                {{ iniciales || 'P' }}
                            </span>
                        </div>

                        <label v-if="editando"
                            class="absolute -bottom-2 -right-2 cursor-pointer rounded-2xl bg-[var(--primary)] px-4 py-2 text-[length:var(--font-xs)] font-black text-white shadow-lg transition hover:opacity-90">
                            Foto
                            <input type="file" accept="image/*" class="hidden" @change="emit('change-photo', $event)" />
                        </label>
                    </div>

                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Información del registro
                        </p>

                        <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                            {{ paciente.persona?.nombre }} {{ paciente.persona?.apellido }}
                        </h1>

                        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                            Código Paciente: <strong class="text-[var(--primary)]">#{{ paciente.codigo_paciente }}</strong>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link :href="route('paciente.index')" v-if="!tieneRol('Paciente')"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] border-[var(--border)]">
                        Volver
                    </Link>

                    <button v-if="!editando" type="button"
                        class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90"
                        @click="emit('toggle-edit')">
                        Editar
                    </button>

                    <button v-if="editando" type="button"
                        class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] border-[var(--border)]"
                        @click="emit('cancel-edit')">
                        Cancelar
                    </button>

                    <button v-if="editando" type="button" :disabled="processing"
                        class="rounded-2xl bg-[var(--primary)] px-5 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60 shadow-lg shadow-teal-100 dark:shadow-none"
                        @click="emit('save')">
                        {{ processing ? 'Guardando...' : 'Guardar cambios' }}
                    </button>
                </div>
            </div>
            
            <p v-if="fotoError" class="mt-3 text-[length:var(--font-xs)] font-bold text-red-500 text-center md:text-left">
                {{ fotoError }}
            </p>
        </div>
    </div>
</template>
