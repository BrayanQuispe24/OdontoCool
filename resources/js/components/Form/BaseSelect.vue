<script setup lang="ts">
defineProps<{
    modelValue: string;
    name: string;
    label: string;
    options: Array<{
        value: string;
        label: string;
    }>;
    error?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <div>
        <label
            :for="name"
            class="mb-2 block text-sm font-bold text-[var(--title)]"
        >
            {{ label }}
        </label>

        <select
            :id="name"
            :name="name"
            :value="modelValue"
            class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-sm text-[var(--title)] outline-none focus:ring-4"
            style="
                border-color: var(--border);
                --tw-ring-color: var(--primary-soft);
            "
            @change="emit(
                'update:modelValue',
                ($event.target as HTMLSelectElement).value
            )"
        >
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>

        <p
            v-if="error"
            class="mt-2 text-sm font-bold text-red-500"
        >
            {{ error }}
        </p>
    </div>
</template>