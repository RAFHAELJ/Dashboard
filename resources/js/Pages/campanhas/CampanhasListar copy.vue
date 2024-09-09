<script setup>
import { ref } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue'; 
import RadiosForm from '@/Components/forms/CampanhaForm.vue';

const { props } = usePage();
const campanhas = ref(props.campanhas || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editCampanhas = ref({
  id: null,
  name: '',
  comeco: '',
  fim: '',
  publico: '',
  idade: '',
  tipo: '',
  video: '',
  capa: '',
  tempo: '',
  url: '',
  duracao: ''
});

const search = ref('');

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' }, 
  { text: 'Nome', value: 'name', sortable: true, title: 'Nome', key: 'name' },
  { text: 'Começo', value: 'comeco', sortable: true, title: 'Começo', key: 'comeco' },
  { text: 'Fim', value: 'fim', sortable: true, title: 'Fim', key: 'fim' },
  { text: 'Publico', value: 'publico', sortable: true, title: 'Publico', key: 'publico' },
  { text: 'Idade', value: 'idade', sortable: true, title: 'Idade', key: 'idade' },
  { text: 'Tipo', value: 'tipo', sortable: true, title: 'Tipo', key: 'tipo' },
  { text: 'Video', value: 'video', sortable: true, title: 'Video', key: 'video' },
  { text: 'Capa', value: 'capa', sortable: true, title: 'Capa', key: 'capa' },
  { text: 'Tempo', value: 'tempo', sortable: true, title: 'Tempo', key: 'tempo' },
  { text: 'Url', value: 'url', sortable: true, title: 'Url', key: 'url' },
  { text: 'Duração', value: 'duracao', sortable: true, title: 'Duração', key: 'duracao' },
  { text: 'Ações', value: 'actions', sortable: false },
];

const deleteCampanhas = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta campanha?')) {
    try {
      await router.delete(route('campanhas.destroy', id));
      campanhas.value = campanhas.value.filter(campanha => campanha.id !== id);
    } catch (error) {
      console.error('Erro ao deletar campanha:', error);
    }
  }
};

const handleCreateItem = () => {
  isEditing.value = false;
  editCampanhas.value = {
    id: null,
    name: '',
    comeco: '',
    fim: '',
    publico: '',
    idade: '',
    tipo: '',
    video: '',
    capa: '',
    tempo: '',
    url: '',
    duracao: ''
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editCampanhas.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteCampanhas(item.id);
};
</script>

<template>
  <Head title="Campanhas" />

  <AuthenticatedLayout>
    <v-container fluid fill-height>
      <DataList
        :headers="headers"
        :items="campanhas"
        :columnTitles="[]"
        searchPlaceholder="Pesquisar Campanhas Radio"
        createButtonLabel="Add Campanha Radio"
        @create="handleCreateItem"
        @edit="handleEditItem"
        @delete="handleDeleteItem"
        :item-key="'id'"
      />

      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <RadiosForm
          :formData="editCampanhas"
          :fields="{
            name: { label: 'Nome da campanha', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
            periodo: { label: 'Período da campanha', type: 'date-range', start: 'comeco', startLabel: 'Começo', end: 'fim', endLabel: 'Fim', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            radio: { label: 'Rádios da campanha', rules: [(v) => !!v || 'Rádio é obrigatório'], required: true },
            publico: { label: 'Público', type: 'select', options: ['Homens', 'Mulheres', 'Todos'], required: true },
            idade: { label: 'Idade mínima', type: 'date-range', start: 'minimo', startLabel: 'Mínima', end: 'maxima', endLabel: 'Máxima', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            tipo: { label: 'Tipo de anúncio', type: 'radio', options: [{ text: 'Video', value: 'video' }, { text: 'Imagem', value: 'imagem' }], required: true },
            permissions: { label: 'Permissões', type: 'checkbox', options: [{ text: 'Ler', value: 'read' }, { text: 'Escrever', value: 'write' }, { text: 'Executar', value: 'execute' }], required: true },
            tempo: { label: 'Duração', type: 'slider', min: 1, max: 60, required: true }
          }"
          :isEditing="isEditing"
          title="Campanha Radio"
          createRoute="campanhas.store"
          updateRoute="campanhas.update"
          @cancel="closeEditModal"
        />
      </v-dialog>
    </v-container>
  </AuthenticatedLayout>
</template>
