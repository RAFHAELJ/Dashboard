<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import RadiusForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const radius = ref(props.radius || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editRadius = ref({
  id: null,
    nome: '', 
    porta: '',
    db_host: '',     
    porta: '',
    db_user: '',
    db_password: '',      
    db_name: '',
    info: '',
    type: 'radius'

});

const search = ref('');

const headers = [
  { text: 'Nome', value: 'nome', sortable: true, title: 'Nome', key: 'nome' },
  { text: 'Host', value: 'db_host', sortable: true, title: 'Host', key: 'db_host' },
  { text: 'Porta', value: 'porta', sortable: true, title: 'Porta', key: 'porta' },
  { text: 'Usuário', value: 'db_user', sortable: true, title: 'Usuário', key: 'db_user' },  
  { text: 'Nome do RADIUS', value: 'db_name', sortable: true, title: 'Nome do RADIUS', key: 'db_name' },
  { text: 'Info', value: 'info', sortable: true, title: 'Info', key: 'info' },
  { text: 'Ações', value: 'actions', sortable: false }
];

const deleteRadius = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta configuração de radiuss?')) {
    try {
      await router.delete(route('radius.destroy', id));
      radius.value = radius.value.filter(item => item.id !== id);
    } catch (error) {
      console.error('Erro ao deletar a configuração de radiuss:', error);
    }
  }
};

const handleCreateItem = () => {
  isEditing.value = false;
  editRadius.value = {
    id: null,
    nome: '', 
    porta: '',
    db_host: '',     
    porta: '',
    db_user: '',
    db_password: '',      
    db_name: '',
    info: '',
    type: 'radius'
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editRadius.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteRadius(item.id);
};
const fetchPage = (page) => {  
  router.get(route('radius.index', { page }), {
    preserveState: true, // Mantém o estado da página
    onSuccess: (page) => {
      radius.value = page.props.radius;
    },
  });
};

</script>

<template>
  <Head title="Configuração de RaDIUS" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="radius"
          :columnTitles="[]"
          searchPlaceholder="Pesquisar RaDIUS"
          createButtonLabel="Adicionar RaDIUS"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="radius"
          @page-changed="fetchPage"
        />

        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <RadiusForm
            :formData="editRadius"
            :showCreateRegiao="true"
            :fields="{
              nome: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
              db_host: { label: 'IP', rules: [(v) => !!v || 'Host é obrigatório'], required: true, type: 'text' },
              porta: { label: 'Porta', rules: [(v) => !!v || 'Porta é obrigatória'], required: true, type: 'text' },
              db_user: { label: 'Usuário', rules: [(v) => !!v || 'Usuário é obrigatório'], required: true, type: 'text' },
              db_password: { label: 'secrets', rules: [(v) => !!v || 'Senha é obrigatória'], required: true, type: 'text' },              
              info: { label: 'Informações', rules: [(v) => !!v || 'Informações são obrigatórias'], required: true }
            }"
            :isEditing="isEditing"
            title="Configuraçao Radius"
            createRoute="radius.store"
            updateRoute="radius.update"
            returnRoute="radius.index"
            @cancel="closeEditModal"
           
          />
        </v-dialog>
      </v-container>
    </template> 
  </AuthenticatedLayout>
</template>
