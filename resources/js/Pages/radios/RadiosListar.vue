<script setup>
import { ref,  } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataList from '@/Components/DataList.vue';
import UserForm from '@/Components/forms/UserForm.vue';
import MacHistoryModal from '@/Components/MacHistoryModal.vue';


const isHistoryModalOpen = ref(false);
const macHistory = ref([]);
const { props } = usePage();
const radios = ref(props.radios || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editRadios = ref({
  id: null,
  radio: '',      
  mac:'',
  geo:'',
  endereco:'',
  info:'',
  controladora: null 
});

const search = ref('');

const openHistoryModal = async (id) => {
  try {
    // Carregar o histórico de MACs via API
    const response = await axios.get(route('radios.machistory', id));
    macHistory.value = response.data;
    isHistoryModalOpen.value = true;
  } catch (error) {
    console.error('Erro ao buscar histórico de MAC:', error);
  }
};

const headers = [
  { text: 'ID', value: 'id', sortable: true, title: 'ID', key: 'id' }, 
  { text: 'Radio', value: 'radio', sortable: true, title: 'Radio', key: 'radio' },
  { text: 'Mac', value: 'mac', sortable: true, title: 'Mac', key: 'mac' },
  { text: 'Geo', value: 'geo', sortable: true, title: 'Geo', key: 'geo'  },
  { text: 'Endereço', value: 'endereco', sortable: true, title: 'Endereço', key: 'endereco' },
  { text: 'Info', value: 'info', sortable: true, title: 'Info', key: 'info' },
  { text: 'Controladora', value: 'controladora.nome', sortable: true, title: 'Controladora', key: 'controladora.nome' },
  { text: 'Regiao', value: 'regiao.cidade', sortable: true, title: 'Regiao', key: 'regiao.cidade' },

  { text: 'Ações', value: 'actions', sortable: false },
];

const deleteRadios = async (id) => {
  if (confirm('Tem certeza que deseja deletar este radio?')) {
    try {        
      await router.delete(route('radios.destroy', id));
      radios.value = radios.value.filter(radio => radio.id !== id);
    } catch (error) {
      console.error('Erro ao deletar radio:', error);
    }
  }
};

const handleViewItem = (item) => {
  console.log('Visualizar item:', item);
};

const handleCreateItem = () => {
  isEditing.value = false;
  editRadios.value = {
    id: null,
    radio: '',      
    mac:'',
    geo:'',
    info:'',
    regiao:'',
    controladora: null 
  };
  isEditModalOpen.value = true;
};

const handleEditItem = (item) => {
  isEditing.value = true;
  editRadios.value = { ...item };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const handleDeleteItem = (item) => {
  deleteRadios(item.id);
};




</script>

<template>
  <Head title="Radios" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="radios"
          :columnTitles="['radio','mac']"
          searchPlaceholder="Pesquisar Usuários Radio"
          createButtonLabel="Adicionar Radio"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="radios"
          :showControladoraLink="true"
          :onCustomAction="openHistoryModal"
        />
        <!-- Adicione o modal de histórico de MACs -->
        <MacHistoryModal
          v-model:isOpen="isHistoryModalOpen"
          :macHistory="macHistory"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">

          <UserForm
            :showCreateRegiao="true"
            :showCreateControladora="true"
            :formData="editRadios"
            :fields="{
              radio: { label: 'Nome Radio', rules: [(v) => !!v || 'RADIO é obrigatório'], required: true },
              mac: { label: 'Mac adress', rules: [(v) => !!v || 'Mac é obrigatório'], required: true, type: 'text' ,mask:'XX-XX-XX-XX-XX-XX' },
              geo: { label: 'GeoLocalização', rules: [(v) => !!v || 'Geo é obrigatório'], required: true, type: 'text' ,mask: '-##.######, -##.######' },
              endereco: { label: 'Endereço', rules: [(v) => !!v || 'Endereço é obrigatório'], required: true },
              info: { label: 'Info', rules: [(v) => !!v || 'Info é obrigatório'], required: true},    
            }"
            :isEditing="isEditing"
            title="Radios"
            createRoute="radios.store"
            updateRoute="radios.update"
            returnRoute="radios.index"
            @cancel="closeEditModal"
          />
          


        </v-dialog>
      </v-container>
     


    </template> 
    
  </AuthenticatedLayout>
</template>
