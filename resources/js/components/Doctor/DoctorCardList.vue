<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Doctor } from '@/types/doctor';

defineProps<{
    doctores: Doctor[];
    tienePermisoUpdate: boolean;
    tienePermisoDestroy: boolean;
    tienePermisoShow: boolean;
}>();

const emit = defineEmits<{
    edit: [doctor: Doctor];
    delete: [codigo: string];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:hidden">
        <article v-for="doctor in doctores" :key="doctor.codigo_doctor"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100">
                    {{ doctor.persona?.nombre?.charAt(0).toUpperCase() ?? 'P' }}
                </div>

                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ doctor.persona?.nombre ?? 'Sin nombre' }}
                    {{ doctor.persona?.apellido ?? '' }}
                </h2>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    {{ doctor.persona?.usuarios?.[0]?.email ?? 'Sin email' }}
                </p>

                <div class="mt-5 space-y-2 text-[length:var(--font-sm)] text-[var(--muted)] border-t pt-4"
                    style="border-color: var(--border)">
                    <p>
                        <span class="font-bold text-[var(--title)]">CI:</span>
                        {{ doctor.persona_id }}
                    </p>

                    <p>
                        <span class="font-bold text-[var(--title)]">Fecha de contratación:</span>
                        {{ doctor.fecha_contratacion ?? 'Sin fecha' }}
                    </p>

                    <p>
                        <span class="font-bold text-[var(--title)]">Matrícula profesional:</span>
                        {{ doctor.matricula_profesional ?? 'Sin matrícula' }}
                    </p>

                    <p v-if="doctor.persona?.telefono">
                        <span class="font-bold text-[var(--title)]">Teléfono:</span>
                        {{ doctor.persona.telefono }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-between border-t pt-4"
                    style="border-color: var(--border)">
                    <span class="rounded-full px-4 py-2 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                        :class="doctor.persona?.usuarios?.[0]?.estado === 'inactivo'
                            ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20'
                            : 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'">
                        {{ doctor.persona?.usuarios?.[0]?.estado ?? 'activo' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <button v-if="tienePermisoUpdate" type="button" @click="emit('edit', doctor)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Editar doctor">
                            ✎
                        </button>

                        <button v-if="tienePermisoDestroy" type="button" @click="emit('delete', doctor.codigo_doctor)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                            title="Eliminar doctor">
                            🗑
                        </button>

                        <Link v-if="tienePermisoShow" :href="route('doctor.show', doctor.codigo_doctor)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Configurar doctor">
                            ⚙
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
