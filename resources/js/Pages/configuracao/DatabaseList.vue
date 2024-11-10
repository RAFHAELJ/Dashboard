<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import DatabaseForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const databases = ref(props.databases || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editDatabase = ref({
  id: null,
    nome: '', 
    porta: '',
    db_host: '',     
    porta: '',
    db_user: '',
    db_password: '',      
    db_name: '',
    info: '',
    type: 'database'

});

const search = ref('');

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' }, 
  { text: 'Nome', value: 'nome', sortable: true, title: 'Nome', key: 'nome' },
  { text: 'Host', value: 'db_host', sortable: true, title: 'Host', key: 'db_host' },
  { text: 'Porta', value: 'porta', sortable: true, title: 'Porta', key: 'porta' },
  { text: 'Usuário', value: 'db_user', sortable: true, title: 'Usuário', key: 'db_user' },  
  { text: 'Nome do Banco', value: 'db_name', sortable: true, title: 'Nome do Banco', key: 'db_name' },
  { text: 'Info', value: 'info', sortable: true, title: 'Info', key: 'info' },
  { text: 'Ações', value: 'actions', sortable: false }
];

const deleteDatabase = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta configuração de banco de dados?')) {
    try {
      await router.delete(route('database.destroy', id));
      databases.value = databases.value.filter(item => item.id !== id);
    } catch (error) {
      console.error('Erro ao deletar a configuração de banco de dados:', error);
    }
  }
};

const handleCreateItem = () => {
  isEditing.value = false;
  editDatabase.value = {
    id: null,
    nome: '', 
    porta: '',
    db_host: '',     
    porta: '',
    db_user: '',
    db_password: '',      
    db_name: '',
    info: '',
    type: 'database'
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editDatabase.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteDatabase(item.id);
};
const fetchPage = (page) => {  
  router.get(route('databases.index', { page }), {
    preserveState: true, // Mantém o estado da página
    onSuccess: (page) => {
      databases.value = page.props.databases;
    },
  });
};


</script>

<template>
  <Head title="Configuração de Banco de Dados" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="databases"
          :columnTitles="[]"
          searchPlaceholder="Pesquisar Bases de Dados"
          createButtonLabel="Adicionar Banco de Dados"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="database"
          @page-changed="fetchPage"
        />

        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <DatabaseForm
            :formData="editDatabase"
            :showCreateRegiao="true"
            :fields="{
              nome: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
              db_host: { label: 'Host/IP', rules: [(v) => !!v || 'Host é obrigatório'], required: true, type: 'text' },
              porta: { label: 'Porta', rules: [(v) => !!v || 'Porta é obrigatória'], required: true, type: 'text' },
              db_user: { label: 'Usuário', rules: [(v) => !!v || 'Usuário é obrigatório'], required: true, type: 'text' },
              db_password: { label: 'Senha', rules: [(v) => !!v || 'Senha é obrigatória'], required: true, type: 'text' },
              db_name: { label: 'Nome do Banco', rules: [(v) => !!v || 'Nome do Banco é obrigatório'], required: true },
              info: { label: 'Informações', rules: [(v) => !!v || 'Informações são obrigatórias'], required: true }
            }"
            :isEditing="isEditing"
            title="Configuração de Banco de Dados"
            createRoute="database.store"
            updateRoute="database.update"
            returnRoute="database.index"
            @cancel="closeEditModal"
           
          />
        </v-dialog>
      </v-container>
    </template> 
  </AuthenticatedLayout>
</template>
