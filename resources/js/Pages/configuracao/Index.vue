<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import RegiaoForm from '@/Components/forms/UserForm.vue';

const { props } = usePage();
const regioes = ref(props.regioes || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editRegiao = ref({
  id: null,
  cidade: '',
  bairros: [],
});
const search = ref('');

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' },
  { text: 'Cidade', value: 'cidade', sortable: true, title: 'Cidade', key: 'cidade' },
  { text: 'Info', value: 'bairros', sortable: true, title: 'Info', key: 'bairros' },
  { text: 'Ações', value: 'actions', sortable: false },
];
const deleteRegiao = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta região?')) {
    try {
      await router.delete(route('regioes.destroy', id));

      // Recarrega a página após excluir
      window.location.reload(); 

    } catch (error) {
      console.error('Erro ao deletar região:', error);
    }
  }
};



const handleCreateItem = () => {
  isEditing.value = false;
  editRegiao.value = {
    id: null,
    cidade: '',
    bairros: [],
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editRegiao.value = { ...item }; // Transforma array em string
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteRegiao(item.id);
};
</script>

<template>
  <Head title="Lista de Regiões" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
    <v-container fluid fill-height>
      <DataList
        :headers="headers"
        :items="regioes"
        :columnTitles="['ID', 'Cidade', 'Bairros']"
        searchPlaceholder="Pesquisar Regiões"
        createButtonLabel="Adicionar Região"
        @create="handleCreateItem"
        @edit="handleEditItem"
        @delete="handleDeleteItem"
        :item-key="'id'"
        :canAccess="canAccess" 
        createRoute="regioes"
      />
      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <RegiaoForm
          :formData="editRegiao"
          :fields="{
            cidade: { label: 'Cidade', rules: [(v) => !!v || 'Cidade é obrigatória'], required: true },
            bairros: { label: 'Info ', rules: [(v) => !!v || 's'], required: true },
          }"
          :isEditing="isEditing"
          title="Região"
          createRoute="regioes.store"
          updateRoute="regioes.update"
          returnRoute="regioes.index"
          @cancel="closeEditModal"
        />
      </v-dialog>
    </v-container>
    </template>
  </AuthenticatedLayout>
</template>
