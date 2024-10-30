<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
<<<<<<< HEAD
import FormView from '@/Components/FormViewer.vue';
=======
import FormView from '@/Components/FormView.vue';
>>>>>>> 9208617008df4e468c550c23c1a9267b6ca70a24

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
