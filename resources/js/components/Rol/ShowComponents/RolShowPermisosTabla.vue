<script setup lang="ts">
import { Permiso } from '@/types/Permiso';

defineProps<{
    permisos: Permiso[];
}>();

const emit = defineEmits<{
    'delete': [id: number];
}>();
</script>

<template>
    <div class="mt-5">
        <!-- Tabla para PC -->
        <div
            class="hidden overflow-hidden rounded-2xl border md:block"
            style="border-color: var(--border)"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left">
                    <thead class="bg-[var(--primary-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nombre del permiso</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--border)">
                        <tr
                            v-for="permiso in permisos"
                            :key="permiso.id"
                            class="transition hover:bg-[var(--section-soft)]"
                        >
                            <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                                #{{ permiso.id }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white">
                                        {{ permiso.nombre.charAt(0).toUpperCase() }}
                                    </div>

                                    <div>
                                        <p class="font-black text-[var(--title)]">
                                            {{ permiso.nombre }}
                                        </p>

                                        <p class="text-[length:var(--font-xs)] font-semibold text-[var(--muted)]">
                                            Permiso del sistema.
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-4 py-2 text-[length:var(--font-xs)] font-black"
                                    :class="permiso.estado === 'activo'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'"
                                >
                                    {{ permiso.estado }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <button
                                    type="button"
                                    @click="emit('delete', permiso.id)"
                                    class="text-[length:var(--font-xl)] font-black text-red-500 transition hover:text-red-700"
                                    title="Eliminar permiso"
                                >
                                    🗑
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tarjetas para celular -->
        <div class="grid gap-4 md:hidden">
            <article
                v-for="permiso in permisos"
                :key="permiso.id"
                class="rounded-2xl border bg-[var(--section-soft)] p-4 shadow-sm"
                style="border-color: var(--border)"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white">
                            {{ permiso.nombre.charAt(0).toUpperCase() }}
                        </div>

                        <div>
                            <h3 class="font-black text-[var(--title)]">
                                {{ permiso.nombre }}
                            </h3>

                            <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                #{{ permiso.id }}
                            </p>
                        </div>
                    </div>

                    <span
                        class="inline-flex rounded-full px-3 py-1 text-[length:var(--font-xs)] font-black"
                        :class="permiso.estado === 'activo'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'"
                    >
                        {{ permiso.estado }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between rounded-xl bg-[var(--card)] px-4 py-3">
                    <p class="text-[length:var(--font-sm)] font-semibold text-[var(--muted)]">
                        Permiso del sistema asociado a este rol.
                    </p>

                    <button
                        type="button"
                        @click="emit('delete', permiso.id)"
                        class="text-[length:var(--font-lg)] font-black text-red-500 hover:text-red-700"
                        title="Eliminar permiso"
                    >
                        🗑
                    </button>
                </div>
            </article>
        </div>
    </div>
</template>
