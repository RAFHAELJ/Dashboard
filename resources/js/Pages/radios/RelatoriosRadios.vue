<template>
    <AuthenticatedLayout>
        <v-container>
        <FilterBar :filters="filters" />
        <ReportDisplay :reports="radios.data" />
    
        <!-- Paginação -->
        <v-pagination
            v-model="page"
            :length="radios.last_page"
            @input="fetchData"
        ></v-pagination>
        </v-container>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import FilterBar from '@/Components/FilterBar-old.vue';
  import ReportDisplay from '@/Components/ReportDisplay.vue';
  import { ref } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  const { props } = usePage();
  const radios = ref(props.radios);
  const filters = ref(props.filters);
  const page = ref(radios.value.current_page);
  
  // Função para buscar novos dados ao mudar a página
  const fetchData = (page) => {
    // Faça uma requisição para o backend para buscar a nova página com os filtros atuais
  };
  </script>
  