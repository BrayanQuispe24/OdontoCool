<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Secretaria } from '@/types/Secretaria';

defineProps<{
    secretarias: Secretaria[];
    tienePermiso: (permiso: string) => boolean;
}>();

const emit = defineEmits<{
    edit: [secretaria: Secretaria];
    delete: [codigo: string];
}>();
</script>

<template>
    <!-- Tarjetas para móvil/tablet -->
    <div class="grid grid-cols-1 gap-5 md:hidden">
        <article v-for="secretaria in secretarias" :key="secretaria.codigo_secretaria"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)] border-[var(--border)]">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100 shrink-0">
                    {{ secretaria.persona?.nombre?.charAt(0).toUpperCase() ?? 'S' }}
                </div>

                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ secretaria.persona?.nombre ?? 'Sin nombre' }} {{ secretaria.persona?.apellido ?? '' }}
                </h2>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    {{ secretaria.persona?.usuarios?.[0]?.email ?? 'Sin email' }}
                </p>

                <div class="mt-5 space-y-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    <p>
                        <span class="font-bold text-[var(--title)]">CI:</span>
                        {{ secretaria.persona_id }}
                    </p>

                    <p>
                        <span class="font-bold text-[var(--title)]">Fecha de contratación:</span>
                        {{ secretaria.fecha_contratacion ?? 'Sin fecha' }}
                    </p>

                    <p v-if="secretaria.persona?.telefono">
                        <span class="font-bold text-[var(--title)]">Teléfono:</span>
                        {{ secretaria.persona.telefono }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-between border-t pt-4 border-[var(--border)]">
                    <span class="rounded-full px-4 py-2 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                        :class="secretaria.persona?.usuarios?.[0]?.estado === 'inactivo'
                            ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20'
                            : 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'">
                        {{ secretaria.persona?.usuarios?.[0]?.estado ?? 'activo' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="emit('edit', secretaria)"
                            v-if="tienePermiso('secretaria.update')"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                            title="Editar secretaria">
                            ✎
                        </button>

                        <button type="button" @click="emit('delete', secretaria.codigo_secretaria)"
                            v-if="tienePermiso('secretaria.destroy')"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500 text-lg"
                            title="Eliminar secretaria">
                            🗑
                        </button>

                        <Link :href="route('secretaria.show', secretaria.codigo_secretaria)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                            title="Ver perfil / turnos">
                            ⚙
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
