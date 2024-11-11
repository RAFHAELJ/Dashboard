<script setup>
import { ref } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import UsuariosForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const usuarios = ref(props.usuarios || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editUsuarios = ref({
  id: null,
  nome: '',
  email: '',
  Value: '',
  telefone: '',
});
const search = ref('');

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' },
  { text: 'Nome', value: 'nome', sortable: true, title: 'Nome', key: 'nome' },
  { text: 'Email', value: 'email', sortable: true, title: 'Email', key: 'email' },  
  { text: 'Acesso', value: 'Value', sortable: true, title: 'Acesso', key: 'Value' },
  { text: 'Telefone', value: 'telefone', sortable: true, title: 'Telefone', key: 'telefone' },
  { text: 'Ações', value: 'actions', sortable: false },
];

const deleteUsuarios = async (id) => {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    try {      
      const userArray = usuarios.value.data ? usuarios.value.data : [];
      await router.delete(route('usuarios.destroy', id));      
      usuarios.value.data = userArray.filter(usuario => usuario.id !== id);
      window.location.reload(); 
    } catch (error) {
      console.error('Erro ao deletar usuário:', error);
    }
  }
};
// Funcionalidade de pesquisa em tempo real

let debounceTimeout = null;
const handleSearchUpdate = (newSearch) => {
  search.value = newSearch;
  if (debounceTimeout) clearTimeout(debounceTimeout);

  debounceTimeout = setTimeout(() => {   
   
    fetchPage({ page: 1, itemsPerPage: 50 }); // Recarrega a página inicial com novos resultados
  }, 1300);
  
};
// Funcionalidade de pesquisa em tempo real
const fetchPage = ({ page, itemsPerPage }) => {  
  
  router.get(route('usuarios.index', { page, search: search.value, per_page:itemsPerPage }), {
    preserveState: true,
    onSuccess: (response) => {
      usuarios.value = response.props.usuarios;
     
    },
  });
};

const handleCreateItem = () => {
  isEditing.value = false;
  editUsuarios.value = { id: null, name: '', email: '', Value: '', telefone: '' };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => { 
  isEditing.value = true;
  editUsuarios.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteUsuarios(item.id);
};
</script>

<template>
  <Head title=" Usuários" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }"> 
      <v-container fluid fill-height>
        <DataList
          :search="search"
          :headers="headers"
          :items="usuarios"
          searchPlaceholder="Pesquisar Usuários Wifi"
          createButtonLabel="Add Usuarios Wifi"
          :showCreateButton="false"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="usuarios"
          @options-changed="fetchPage"
          @search-updated="handleSearchUpdate"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <UsuariosForm
            v-if="isEditModalOpen"
            :formData="editUsuarios"
            :isEditing="isEditing"
            :fields="{
              nome: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true, autocomplete: 'nome' },
              email: { label: 'Email', rules: [(v) => !!v || 'Email é obrigatório', (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido'], required: true, autocomplete: 'email' },    
              Value: { label: 'Acesso', rules: [(v) => !!v || 'Acesso é obrigatório', (v) => /.+@.+\..+/.test(v)], required: true },    
              telefone: { label: 'Telefone', rules: [] },               
            }"
            createRoute="usuarios.store"
            updateRoute="usuarios.update"
            returnRoute="usuarios.index"
            @cancel="closeEditModal"
          />
        </v-dialog>
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>

