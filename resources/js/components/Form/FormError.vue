<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p
        v-if="message"
        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500"
    >
        {{ message }}
    </p>
</template>