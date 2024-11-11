<script setup>
import { ref } from 'vue';
import { Head, usePage,router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';

const { props } = usePage();
const logs = ref(props.logs || []); // Recebendo os logs do Inertia
const search = ref('');

const headers = [
  { text: 'Data', value: 'data', sortable: true, title: 'Data', key: 'data' },
  { text: 'Usuário', value: 'user.name', sortable: true, title: 'Usuário', key: 'user.name' },
  { text: 'Ação', value: 'acao', sortable: true, title: 'Ação', key: 'acao' },

 
];


let debounceTimeout = null;
const handleSearchUpdate = (newSearch) => {
  search.value = newSearch;
  if (debounceTimeout) clearTimeout(debounceTimeout);

  debounceTimeout = setTimeout(() => {   
   
    fetchPage({ page: 1, itemsPerPage: 50 }); 
  }, 1300);
  
};

const fetchPage = ({ page, itemsPerPage }) => {  
  
  router.get(route('logs.index', { page, search: search.value, per_page:itemsPerPage }), {
    preserveState: true,
    onSuccess: (response) => {
      logs.value = response.props.logs;
     
    },
  });
};
const handleCreate = () => {
  // Lógica para criação
};

const handleEdit = (item) => {
  // Lógica para edição
};

const handleDelete = (item) => {
  // Lógica para exclusão
};

</script>

<template>
  <Head title="Logs de Ações" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="logs"
          searchPlaceholder="Pesquisar Logs"
          :showCreateButton="false"
          :item-key="'id'"
          :canAccess="canAccess"             
          :createRoute="'/caminho/para/criar'" 
          @create="handleCreate"
          @edit="handleEdit"
          @delete="handleDelete"
          @options-changed="fetchPage"
          @search-updated="handleSearchUpdate"
          
        />
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>
