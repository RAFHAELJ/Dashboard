<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 ">
        <div>
            <Link href="/">
                <img src="../../images/logo.svg" alt="Logo" />

            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
.custom-blue-background {
    background-color: rgb(242, 222, 222); /* Exemplo de um tom de azul */
}
</style>
