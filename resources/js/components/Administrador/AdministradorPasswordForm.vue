<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import BaseInput from '../Form/BaseInput.vue';


const props = defineProps<{
    codigoUsuario: string;
}>();
const passwordForm = useForm({ password: '', password_confirmation: '', });
const cambiarPassword = () => {
    passwordForm.patch(route('usuario.cambiar-password',
        props.codigoUsuario,),
        {
            preserveScroll: true,
            onSuccess: () => { passwordForm.reset(); },
        },);
}; 
</script>
<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300"
        style="border-color: var(--border)">
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]"> Cambiar
            contraseña </p>
        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]"> Esta acción cambiará la contraseña de acceso
            de esta cuenta. </p>
        <form class="mt-5 grid gap-5 sm:grid-cols-2" @submit.prevent="cambiarPassword">
            <BaseInput v-model="passwordForm.password" name="password" label="Nueva contraseña" type="password"
                placeholder="Mínimo 8 caracteres" :error="passwordForm.errors.password" />
            <BaseInput v-model="passwordForm.password_confirmation" name="password_confirmation"
                label="Confirmar contraseña" type="password" placeholder="Repite la contraseña"
                :error="passwordForm.errors.password_confirmation" />
            <div class="flex justify-end sm:col-span-2"> <button type="submit" :disabled="passwordForm.processing"
                    class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ passwordForm.processing ? 'Actualizando...' : 'Cambiar contraseña' }} </button> </div>
        </form>
    </div>
</template>