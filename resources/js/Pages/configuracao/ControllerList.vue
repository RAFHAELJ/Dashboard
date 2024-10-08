<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import controladoraForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const controladora = ref(props.controladora || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editcontroladora = ref({
  id: null,
  nome: '', 
  ip: '',     
  porta: '',  
  senha: '', 
  info: '',
  regiao: '',
  type: 'controller'
});

const search = ref('');

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' }, 
  { text: 'Nome', value: 'nome', sortable: true, title: 'Nome', key: 'nome' },
  { text: 'IP', value: 'ip', sortable: true, title: 'IP', key: 'ip' },
  { text: 'Porta', value: 'porta', sortable: true, title: 'Porta', key: 'porta' }, 
  { text: 'Senha', value: 'senha', sortable: true, title: 'Senha', key: 'senha' },
  { text: 'Info', value: 'info', sortable: true, title: 'Info', key: 'info' },
  { text: 'Região', value: 'regiao.cidade', sortable: true, title: 'Região', key: 'regiao.cidade' },
  { text: 'Ações', value: 'actions', sortable: false }
];

const deletecontroladora = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta controladora?')) {
    try {
      await router.delete(route('controladora.destroy', id));
      controladora.value = controladora.value.filter(item => item.id !== id);
    } catch (error) {
      console.error('Erro ao deletar a controladora:', error);
    }
  }
};

const handleCreateItem = () => {
  isEditing.value = false;
  editcontroladora.value = {
    id: null,
    nome: '', 
    ip: '',     
    porta: '',   
    senha: '',      
    info: '',
    regiao: '',
    type: 'controller'
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editcontroladora.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deletecontroladora(item.id);
};

const savecontroladora = async () => {
  try {
    if (isEditing.value) {
      await router.put(route('controladora.update', editcontroladora.value.id), editcontroladora.value);
    } else {
      await router.post(route('controladora.store'), editcontroladora.value);
    }
    // Atualiza a lista de controladoras após salvar
    closeEditModal();
    // Recarrega a página ou atualiza o componente DataList, se necessário
  } catch (error) {
    console.error('Erro ao salvar a controladora:', error);
  }
};
</script>

  
<template>
  <Head title="Controladora" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="controladora"
          :columnTitles="['ID', 'Nome', 'IP', 'Porta', 'Usuário', 'Senha', 'Endereço', 'Info', 'Região']"
          searchPlaceholder="Pesquisar Controladoras"
          createButtonLabel="Adicionar Controladora"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="controladora"
        />

        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <controladoraForm
            :showCreateRegiao="true"
            :formData="editcontroladora"
            :fields="{
              nome: { label: 'Nome', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
              ip: { label: 'IP', rules: [(v) => !!v || 'IP é obrigatório'], required: true, type: 'text' },
              porta: { label: 'Porta', rules: [(v) => !!v || 'Porta é obrigatória'], required: true, type: 'text' },              
              senha: { label: 'Senha', rules: [(v) => !!v || 'Senha é obrigatória'], required: true, type: 'password' },              
              info: { label: 'Informações', rules: [(v) => !!v || 'Informações são obrigatórias'], required: true }
            }"
            :isEditing="isEditing"
            title="Controladora"
            createRoute="controladora.store"
            updateRoute="controladora.update"
            returnRoute="controladora.index"
            @cancel="closeEditModal"
            @submit.prevent="savecontroladora"
          />
        </v-dialog>
      </v-container>
    </template> 
  </AuthenticatedLayout>
</template>

  