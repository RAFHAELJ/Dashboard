<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const users = ref(props.users || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editUser = ref({
  id: null,
  name: '',
  email: '',
});
const search = ref('');

const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Nome', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Ações', value: 'actions', sortable: false },
];

const filteredUsers = computed(() => {
  const searchValue = search.value.toLowerCase();
  return users.value.filter((user) => {
    const name = user.name ? user.name.toLowerCase() : '';
    const email = user.email ? user.email.toLowerCase() : '';
    return name.includes(searchValue) || email.includes(searchValue);
  });
});

const tableHeight = computed(() => {
  return `calc(100vh - 160px)`;
});

const deleteUser = async (id) => {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    try {
      await router.delete(route('users.destroy', id));
      users.value = users.value.filter(user => user.id !== id);
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
  editUser.value = {
    id: null,
    name: '',
    email: '',
    password: ''
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editUser.value = { ...item };
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
      <v-row>
        <v-col cols="12">
          <v-card flat class="rounded-lg shadow">
            <v-card-title class="d-flex align-center justify-between flex-wrap pe-2">
              <div class="d-flex align-center">
                <v-icon icon="mdi-account-multiple" class="me-2"></v-icon>
                Usuários Dashboard
              </div>

              <v-text-field
                v-model="search"
                density="compact"
                label="Pesquisar"
                prepend-inner-icon="mdi-magnify"
                variant="solo-filled"
                hide-details
                single-line
                class="search-field"
              ></v-text-field>

              <v-btn
                @click="handleCreateItem"
                density="comfortable"
                color="primary"
                class="add-button"
              >
                <v-icon start>mdi-plus</v-icon>
                Adicionar
              </v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-data-table
              :headers="headers"
              :items="filteredUsers"
              item-key="id"
              class="elevation-1"
              :height="tableHeight"
            >
              <template v-slot:item="{ item, index }">
                <v-card :elevation="index % 2 === 0 ? 4 : 1" class="mb-2 me-0 pa-">
                  <v-row align="center">
                    <v-col cols="2">{{ item.id }}</v-col>
                    <v-col cols="4" class="d-flex align-center">
                      <v-avatar :color="item.avatar ? '' : 'surface-variant'" class="ma-3">
                        <img :src="item.avatar || '/default-avatar.png'" alt="Avatar" />
                      </v-avatar>
                      {{ item.name }}
                    </v-col>
                    <v-col cols="4">{{ item.email }}</v-col>
                    <v-col cols="" class="text-center">
                      <v-btn variant="plain" density="compact" icon="mdi-pencil-outline" @click="handleEditItem(item)"> </v-btn>
                      <v-btn variant="plain" density="compact" icon="mdi-trash-can-outline" @click="handleDeleteItem(item)"> </v-btn>
                      <VMenu :close-on-content-click="true">
                        <template v-slot:activator="{ props }">
                          <v-btn variant="plain" density="compact" icon="mdi-dots-vertical" v-bind="props"> </v-btn>
                        </template>
                        <VSheet rounded="md" width="200" elevation="10" class="mt-2">
                          <v-list lines="one" density="compact" class="pa-0" color="primary">
                            <v-list-item @click="handleViewItem(item)">Visualizar</v-list-item>
                            <v-list-item @click="handleEditItem(item)">Editar</v-list-item>
                            <v-list-item @click="handleDeleteItem(item)">Deletar</v-list-item>
                          </v-list>
                        </VSheet>
                      </VMenu>
                    </v-col>
                  </v-row>
                </v-card>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>

      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <UserForm
          v-if="isEditModalOpen"
          :user="editUser"
          :isEditing="isEditing"
          @cancel="closeEditModal"
        />
      </v-dialog>
    </v-container>
  </AuthenticatedLayout>
</template>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}

.v-avatar img {
  object-fit: cover;
  border-radius: 50%;
}

/* Campo de busca */
.search-field {
  max-width: 300px;
  margin-right: 10px;
}

/* Botão de adicionar */
.add-button {
  border-radius: 50px;
  text-transform: uppercase;
}

@media (max-width: 768px) {
  .v-card-title {
    flex-direction: column;
    align-items: flex-start;
  }

  .search-field {
    width: 100%;
    margin-top: 10px;
  }

  .add-button {
    width: 100%;
    margin-top: 10px;
  }
}
</style>
