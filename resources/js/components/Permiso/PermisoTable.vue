<script setup lang="ts">
import type { Permiso } from '@/types/Permiso';

defineProps<{
    permisos: Permiso[];
}>();

const emit = defineEmits<{
    edit: [permiso: Permiso];
    delete: [permisoId: number];
}>();
</script>

<template>
    <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-[length:var(--font-sm)]">
                    <thead class="bg-[var(--text)] text-[var(--card)]">
                        <tr>
                            <th class="px-6 py-4 font-black">ID</th>
                            <th class="px-6 py-4 font-black">Nombre</th>
                            <th class="px-6 py-4 font-black">Estado</th>
                            <th class="px-6 py-4 text-right font-black">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="permiso in permisos" :key="permiso.id"
                            class="transition hover:bg-[var(--section-soft)]/50">
                            <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                                #{{ permiso.id }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-black text-[var(--title)]">
                                    {{ permiso.nombre }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span v-if="permiso.estado === 'activo'"
                                    class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    Activo
                                </span>
                                <span v-else
                                    class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500">
                                    Inactivo
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="emit('edit', permiso)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                        title="Editar permiso">
                                        ✎
                                    </button>

                                    <button type="button" @click="emit('delete', permiso.id)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500 text-lg"
                                        title="Eliminar permiso">
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="grid gap-4 p-4 md:hidden">
            <article v-for="permiso in permisos" :key="permiso.id"
                class="rounded-2xl border bg-[var(--card)] p-5 shadow transition-colors duration-300 border-[var(--border)]">
                <div class="flex items-center justify-between">
                    <h3 class="font-black text-[var(--title)]">
                        {{ permiso.nombre }}
                    </h3>

                    <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                        #{{ permiso.id }}
                    </span>
                </div>

                <div class="mt-3">
                    <span v-if="permiso.estado === 'activo'"
                        class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                        Activo
                    </span>
                    <span v-else
                        class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500">
                        Inactivo
                    </span>
                </div>

                <div class="mt-4 flex gap-2 border-t pt-4 border-[var(--border)]">
                    <button type="button" @click="emit('edit', permiso)"
                        class="flex-1 rounded-xl bg-[var(--section-soft)] px-4 py-2.5 text-[length:var(--font-sm)] font-black text-[var(--primary)] transition hover:opacity-80 border border-[var(--border)]">
                        Editar
                    </button>

                    <button type="button" @click="emit('delete', permiso.id)"
                        class="flex-1 rounded-xl bg-rose-500/10 px-4 py-2.5 text-[length:var(--font-sm)] font-black text-rose-600 transition hover:bg-rose-500/20 border border-rose-500/20">
                        Eliminar
                    </button>
                </div>
            </article>
        </div>
    </div>
</template>
