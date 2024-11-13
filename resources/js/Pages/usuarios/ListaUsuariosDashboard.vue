<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import UserForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const isAdmin = ref(props.auth.user.nivel === 'Administrador');
const isSuper = ref(props.auth.user.nivel === 'Supervisor');
const users = ref(props.users || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editUser = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  regiao: '',
  nivel: '',
});
const search = ref('');

const headers = [
  { text: 'ID', value: 'id' , sortable: true,title:'ID',key:'id'},
  { text: 'Nome', value: 'name', sortable: true,title:'Nome',key:'name' },
  { text: 'Email', value: 'email' , sortable: true,title:'Email',key:'email'},
  { text: 'Regiao', value: 'regiao.cidade' , sortable: true,title:'Regiao',key:'regiao.cidade'},
  { text: 'Nivel', value: 'nivel' , sortable: true,title:'Nivel',key:'Nivel'},
  { text: 'Ações', value: 'actions', sortable: false },
];

const deleteUser = async (id) => {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    try {      
      const userArray = users.value.data ? users.value.data : [];
      await router.delete(route('users.destroy', id));      
      
      users.value.data = userArray.filter(user => user.id !== id);
     
      window.location.reload(); 
      
    } catch (error) {
      console.error('Erro ao deletar usuário:', error);
    }
  }
};


const handleCreateItem = () => {
  isEditing.value = false;
  editUser.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    regiao: '',
    nivel: '',
    
  };
  isEditModalOpen.value = true;
};

let debounceTimeout = null;
const handleSearchUpdate = (newSearch) => {
  search.value = newSearch;
  if (debounceTimeout) clearTimeout(debounceTimeout);

  debounceTimeout = setTimeout(() => {   
   
    fetchPage({ page: 1, itemsPerPage: 50 }); // Recarrega a página inicial com novos resultados
  }, 1300);
  
};

const fetchPage = ({ page, itemsPerPage }) => {  
  
  router.get(route('users.index', { page, search: search.value, per_page:itemsPerPage }), {
    preserveState: true,
    onSuccess: (response) => {
      users.value = response.props.users;
     
    },
  });
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editUser.value = { 
    ...item, 
    password: '', // Inicializando o campo password vazio
    password_confirmation: '' // Inicializando o campo password_confirmation vazio
  };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteUser(item.id);
};
const nivelOptions = computed(() => {
  if (isAdmin.value) {
    return ['Administrador', 'Operador', 'Marketing', 'Supervisor'];
  } else if (isSuper.value) {
    return ['Operador', 'Marketing', 'Supervisor'];
  } else {
    return ['Operador', 'Marketing'];
  }
});
console.log(nivelOptions.value);
</script>

<template>
  <Head title="Lista de Usuários" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }"> <!-- Recebe a função canAccess via slot -->
      <v-container fluid fill-height>
        <DataList
           :search="search"
          :headers="headers"
          :items="users"
          :columnTitles="[]"
          searchPlaceholder="Pesquisar Usuários"
          createButtonLabel="Adicionar Usuário"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="users"
          @options-changed="fetchPage"
          @search-updated="handleSearchUpdate"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <UserForm
            :formData="editUser"
            :showCreateRegiao="true"
            :fields="{
              name: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true,autocomplete: 'username' },
              email: { label: 'Email', rules: [(v) => !!v || 'Email é obrigatório', (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido'], required: true ,autocomplete: 'email'},    
              nivel: { label: 'Nivel', type: 'select', required: true, items: nivelOptions },
              ...( !isEditing ? {
                  password: { label: 'Senha', rules: [(v) => v.length >= 6 || 'Senha deve ter no mínimo 6 caracteres'], required: true, type: 'password', autocomplete: 'new-password' },
                  password_confirmation: {label: 'Confirmar Senha',rules: [
                    (v) => !!v || 'A confirmação da senha é obrigatória'
                  ], required: true, type: 'password', autocomplete: 'new-password'  }} : {})
            }"
            :isEditing="isEditing"
            title="Usuário"
            createRoute="users.store"
            updateRoute="users.update"
            returnRoute="users.index"           
            @cancel="closeEditModal"
          />
        </v-dialog>
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>

