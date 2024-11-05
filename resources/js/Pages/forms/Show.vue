<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormView from '@/Components/FormViewer.vue';

const { props } = usePage();
const form = ref(props.form || {});
</script>

<template> 
  <Head title="Visualização de Formulário" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <FormView :form="form" @back="$inertia.get(route('forms.index'))" />
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>
