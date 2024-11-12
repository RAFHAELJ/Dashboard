<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
      <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 ">
        <div>
            <Link href="/">
                <img src="../../images/logo.svg" alt="Logo" class="w-40 h-40">
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
