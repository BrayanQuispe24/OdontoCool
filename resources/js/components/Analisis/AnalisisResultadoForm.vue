<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    solicitud: any;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => {
    return props.solicitud?.resultado_analisis !== null && props.solicitud?.resultado_analisis !== undefined;
});

const resultadoForm = useForm({
    _method: 'POST',
    fecha_resultado: new Date().toISOString().split('T')[0],
    resultado: '',
    observaciones: '',
    interpretacion: '',
    archivo_resultado: null as File | string | null,
    solicitud_analisis_id: '',
});

const resetResultadoForm = () => {
    resultadoForm.reset();
    resultadoForm.clearErrors();
};

const cargarResultado = (solicitud: any) => {
    resetResultadoForm();

    if (!solicitud) {
        return;
    }

    const res = solicitud.resultado_analisis;
    if (res) {
        resultadoForm._method = 'PUT';
        resultadoForm.fecha_resultado = res.fecha_resultado;
        resultadoForm.resultado = res.resultado;
        resultadoForm.observaciones = res.observaciones || '';
        resultadoForm.interpretacion = res.interpretacion || '';
        resultadoForm.archivo_resultado = res.archivo_resultado || null;
        resultadoForm.solicitud_analisis_id = String(solicitud.id);
    } else {
        resultadoForm._method = 'POST';
        resultadoForm.fecha_resultado = new Date().toISOString().split('T')[0];
        resultadoForm.resultado = '';
        resultadoForm.observaciones = '';
        resultadoForm.interpretacion = '';
        resultadoForm.archivo_resultado = null;
        resultadoForm.solicitud_analisis_id = String(solicitud.id);
    }
};

watch(
    () => props.solicitud,
    cargarResultado,
    {
        immediate: true,
    },
);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] || null;

    if (file) {
        const maxSizeBytes = 10 * 1024 * 1024;

        if (file.size > maxSizeBytes) {
            resultadoForm.errors.archivo_resultado =
                'El archivo es demasiado grande. El límite máximo permitido es de 10 MB.';
            resultadoForm.archivo_resultado = null;
            target.value = ''; // clear input
            return;
        }
    }
    resultadoForm.clearErrors('archivo_resultado');
    resultadoForm.archivo_resultado = file;
};

const isFile = (value: any): value is File => {
    return value instanceof File;
};

const formatPath = (path?: string) => {
    if (!path) return '';
    const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace('public/', '').replace(/^\/+/, '');
    return `${base}/storage/${cleanPath}`;
};

const submit = () => {
    if (editMode.value && props.solicitud?.resultado_analisis?.id) {
        resultadoForm.post(
            route('resultado_analisis.update', props.solicitud.resultado_analisis.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    resetResultadoForm();
                    emit('saved');
                },
            },
        );
        return;
    }

    resultadoForm.post(route('resultado_analisis.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetResultadoForm();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="mb-4 border-b pb-3" style="border-color: var(--border)">
            <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                {{ editMode ? 'Editar Informe de Resultado' : 'Registrar Resultado de Laboratorio' }}
            </h2>

            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]" v-if="solicitud">
                Carga el resultado de la solicitud
                <strong>#{{ solicitud.id }}</strong> para el análisis
                <strong>"{{ solicitud.analisis?.nombre }}"</strong>.
            </p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha del Resultado
                    </label>

                    <input v-model="resultadoForm.fecha_resultado" type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="resultadoForm.errors.fecha_resultado"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ resultadoForm.errors.fecha_resultado }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Archivo Adjunto / Informe
                    </label>

                    <input type="file" @change="handleFileChange"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-[var(--primary-soft)] file:px-4 file:py-2 file:text-[length:var(--font-xs)] file:font-semibold file:text-[var(--primary)] hover:file:opacity-80 focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <div class="mt-2 flex flex-col gap-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                        <div v-if="
                            resultadoForm.archivo_resultado &&
                            typeof resultadoForm.archivo_resultado === 'string'
                        ">
                            Archivo actual:
                            <a :href="formatPath(resultadoForm.archivo_resultado)" target="_blank"
                                class="font-bold text-[var(--primary)] hover:underline">
                                {{ resultadoForm.archivo_resultado.split('/').pop() }}
                            </a>
                        </div>
                        <div v-else-if="isFile(resultadoForm.archivo_resultado)">
                            Nuevo archivo seleccionado:
                            <span class="font-bold text-slate-700">{{
                                resultadoForm.archivo_resultado.name
                            }}</span>
                        </div>
                    </div>

                    <p v-if="resultadoForm.errors.archivo_resultado"
                        class="mt-2 text-sm font-bold text-red-500">
                        {{ resultadoForm.errors.archivo_resultado }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Resultado Clínico (Hallazgos)
                    </label>

                    <textarea v-model="resultadoForm.resultado" required rows="3"
                        placeholder="Describe los valores obtenidos y hallazgos principales del laboratorio..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="resultadoForm.errors.resultado"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ resultadoForm.errors.resultado }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Interpretación Médica
                    </label>

                    <textarea v-model="resultadoForm.interpretacion" rows="3"
                        placeholder="Comentarios de interpretación y relevancia para el tratamiento..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Observaciones Generales
                    </label>

                    <textarea v-model="resultadoForm.observaciones" rows="2"
                        placeholder="Notas adicionales de laboratorio o administrativas..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4" style="border-color: var(--border)">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="resultadoForm.processing"
                    class="rounded-2xl bg-[var(--text)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</template>
