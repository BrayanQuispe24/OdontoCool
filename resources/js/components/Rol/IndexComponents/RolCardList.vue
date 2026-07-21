<script setup lang="ts">
import { Rol } from '@/types/Rol';
import { Link } from '@inertiajs/vue3';

defineProps<{
    roles: Rol[];
}>();

const emit = defineEmits<{
    'edit': [rol: Rol];
    'delete': [id: number];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article
            v-for="rol in roles"
            :key="rol.id"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)"
        >
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div
                    class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100"
                >
                    {{ rol.nombre.charAt(0).toUpperCase() }}
                </div>

                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ rol.nombre }}
                </h2>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    {{ rol.description }}
                </p>

                <div class="mt-6 flex items-center justify-between">
                    <span
                        v-if="rol.estado === 'activo'"
                        class="rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[var(--primary)] font-bold text-[length:var(--font-xs)]"
                    >
                        👍 Activo
                    </span>

                    <span
                        v-else
                        class="rounded-full bg-red-100 px-4 py-2 text-red-600 font-bold text-[length:var(--font-xs)]"
                    >
                        👎 Inactivo
                    </span>

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="emit('edit', rol)"
                            class="text-[length:var(--font-xl)] font-black text-[var(--primary)] transition group-hover:translate-x-1 hover:text-[var(--primary-hover)]"
                            title="Editar rol"
                        >
                            ✎
                        </button>

                        <button
                            type="button"
                            @click="emit('delete', rol.id)"
                            class="text-[length:var(--font-xl)] font-black text-red-500 transition group-hover:translate-x-1 hover:text-red-700"
                            title="Eliminar rol"
                        >
                            🗑
                        </button>

                        <Link
                            :href="route('rol.show', rol.id)"
                            class="text-[length:var(--font-xl)] font-black text-[var(--primary)] transition group-hover:translate-x-1 hover:text-[var(--primary-hover)]"
                            title="Configurar permisos"
                        >
                            ⚙
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
