<script setup lang="ts">
import FormError from './FormError.vue';


withDefaults(
    defineProps<{
        modelValue: string;
        name: string;
        label: string;
        placeholder?: string;
        rows?: number;
        disabled?: boolean;
        readonly?: boolean;
        error?: string;
    }>(),
    {
        placeholder: '',
        rows: 3,
        disabled: false,
        readonly: false,
        error: '',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <div>
        <label
            :for="name"
            class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]"
        >
            {{ label }}
        </label>

        <textarea
            :id="name"
            :name="name"
            :value="modelValue"
            :rows="rows"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            class="w-full resize-none rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 disabled:cursor-not-allowed disabled:opacity-60"
            style="
                border-color: var(--border);
                --tw-ring-color: var(--primary-soft);
            "
            @input="
                emit(
                    'update:modelValue',
                    ($event.target as HTMLTextAreaElement).value,
                )
            "
        />

        <FormError :message="error" />
    </div>
</template>