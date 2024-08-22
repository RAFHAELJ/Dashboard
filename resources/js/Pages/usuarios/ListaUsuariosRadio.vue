<script setup>
import { ref, computed } from 'vue';
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
  name: '',
  email: '',
});
const search = ref('');

const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Nome', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'CPF', value: 'cpf' },
  { text: 'MAC', value: 'mac' },
  { text: 'Ações', value: 'actions', sortable: false },
];

const deleteUsuarios = async (id) => {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    try {
      await router.delete(route('usuarios.destroy', id));
      usuarios.value = usuarios.value.filter(usuario => usuario.id !== id);
    } catch (error) {
      console.error('Erro ao deletar usuário:', error);
    }
  }
};

const handleViewItem = (item) => {
  console.log('Visualizar item:', item);
};

const handleCreateItem = () => {
  isEditing.value = false;
  editUsuarios.value = {
    id: null,
    name: '',
    email: '',
    cpf: '',
    mac: '',
    password: ''
  };
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
    <v-container fluid fill-height>
      <DataList
        :headers="headers"
        :items="usuarios"
        searchPlaceholder="Pesquisar Usuários Radio"
        createButtonLabel="Add Usuarios Radio"
        @create="handleCreateItem"
        @edit="handleEditItem"
        @delete="handleDeleteItem"
        :item-key="'id'"
      />
      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <UsuariosForm
          v-if="isEditModalOpen"
          :usuario="editUsuarios"
          :isEditing="isEditing"
          @cancel="closeEditModal"
        />

      </v-dialog>
    </v-container>
  </AuthenticatedLayout>
</template>
