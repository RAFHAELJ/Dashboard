<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import UserForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
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
  { text: 'Regiao', value: 'regiao.cidade' , sortable: true,title:'Regiao',key:'regiao'},
  { text: 'Cargo', value: 'nivel' , sortable: true,title:'Cargo',key:'nivel'},
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
</script>

<template>
  <Head title="Lista de Usuários" />

  <AuthenticatedLayout>
    <v-container fluid fill-height>
      <DataList
        :headers="headers"
        :items="users"
        :columnTitles="['ID', 'Nome', 'Email']"
        searchPlaceholder="Pesquisar Usuários"
        createButtonLabel="Adicionar Usuário"
        @create="handleCreateItem"
        @edit="handleEditItem"
        @delete="handleDeleteItem"
        :item-key="'id'"
      />
      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <UserForm
  :formData="editUser"
  :showCreateRegiao="true"
  :fields="{
    name: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true,autocomplete: 'username' },
    email: { label: 'Email', rules: [(v) => !!v || 'Email é obrigatório', (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido'], required: true ,autocomplete: 'email'},    
    nivel: { label: 'Cargo', type: 'select', required: true, items: ['Administrador', 'Cliente', 'Supervisor', 'Cliente Regional', 'Supervisor Regional'] },
    ...( !isEditing ? {
        password: { label: 'Senha', rules: [(v) => v.length >= 6 || 'Senha deve ter no mínimo 6 caracteres'], required: true, type: 'password', autocomplete: 'new-password' },
        password_confirmation: {label: 'Confirmar Senha',rules: [(v) => !!v || 'A confirmação da senha é obrigatória',(v) => v === editUser.password || 'As senhas não coincidem'],required: true, type: 'password', autocomplete: 'new-password'  }
      } : {})
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
  </AuthenticatedLayout>
</template>
