<script setup lang="ts">
defineProps<{
    resultados: any[];
}>();

const emit = defineEmits<{
    edit: [solicitud: any];
    delete: [id: number];
}>();

const formatPath = (path?: string) => {
    if (!path) return '';
    const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace('public/', '').replace(/^\/+/, '');
    return `${base}/storage/${cleanPath}`;
};
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="res in resultados" :key="res.id"
            class="flex flex-col justify-between rounded-3xl border border-[#bcefeb] bg-white p-5 shadow-[0_15px_40px_rgba(0,169,157,0.08)]">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Resultado para Solicitud #{{ res.solicitud?.id }}
                        </span>

                        <h3 class="mt-0.5 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            {{ res.solicitud?.analisis?.nombre || 'Análisis clínico' }}
                        </h3>
                    </div>

                    <span class="rounded-lg border bg-[var(--section-soft)] px-2.5 py-1 text-[length:var(--font-xs)] font-bold text-[var(--primary)]"
                        style="border-color: var(--border)">
                        📅 {{ res.fecha_resultado }}
                    </span>
                </div>

                <div class="space-y-2 rounded-2xl border bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)]"
                    style="border-color: var(--border)">
                    <div>
                        <h4 class="text-[length:var(--font-xs)] font-bold uppercase tracking-wide text-[var(--muted)]">
                            Hallazgos y Resultado
                        </h4>

                        <p class="mt-1 whitespace-pre-line leading-relaxed text-[var(--title)]">
                            {{ res.resultado }}
                        </p>
                    </div>

                    <div v-if="res.interpretacion" class="border-t pt-2" style="border-color: var(--border)">
                        <h4 class="text-[length:var(--font-xs)] font-bold uppercase tracking-wide text-[var(--muted)]">
                            Interpretación médica
                        </h4>

                        <p class="mt-1 whitespace-pre-line italic leading-relaxed text-[var(--muted)]">
                            "{{ res.interpretacion }}"
                        </p>
                    </div>

                    <div v-if="res.observaciones" class="border-t pt-2" style="border-color: var(--border)">
                        <h4 class="text-[length:var(--font-xs)] font-bold uppercase tracking-wide text-[var(--muted)]">
                            Observaciones adicionales
                        </h4>

                        <p class="mt-1 text-[var(--muted)]">
                            {{ res.observaciones }}
                        </p>
                    </div>
                </div>

                <div v-if="res.archivo_resultado"
                    class="inline-flex items-center rounded-xl border border-[#bcefeb] bg-[#f0fbf9] px-3.5 py-2 text-xs shadow-sm hover:bg-[#e6fffb] transition-all">
                    <a :href="formatPath(res.archivo_resultado)"
                        :download="(res.archivo_resultado).split('/').pop()"
                        class="flex items-center gap-2 font-black text-[#00a99d]">
                        <span>📎</span>
                        <span class="truncate max-w-[200px] sm:max-w-xs">
                            {{ (res.archivo_resultado).split('/').pop() }}
                        </span>
                        <span class="text-[10px] text-[#00a99d]/70 font-normal shrink-0">
                            📥 (Descargar)
                        </span>
                    </a>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between border-t pt-4"
                style="border-color: var(--border)">
                <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    Asociado a Tratamiento #{{ res.solicitud?.tratamiento?.id }}
                </span>

                <div class="flex gap-2">
                    <button type="button" @click="emit('edit', res.solicitud)"
                        class="rounded-xl border bg-[var(--card)] px-3.5 py-1.5 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)]"
                        style="border-color: var(--primary)">
                        Editar
                    </button>

                    <button type="button" @click="emit('delete', res.id)"
                        class="rounded-xl border border-red-200 bg-[var(--card)] px-3.5 py-1.5 text-[length:var(--font-xs)] font-black text-red-500 transition hover:bg-red-50">
                        Eliminar
                    </button>
                </div>
            </div>
        </article>
    </div>
</template>
