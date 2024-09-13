<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { VContainer, VRow, VCol, VCard, VCardActions, VCardTitle, VCardText, VBtn, VTextField, VSelect, VDialog, VIcon } from 'vuetify/components';
import CampanhasForm from '@/Components/forms/CampanhaForm.vue';

const radios = ref([]);
const { props } = usePage();
const campanhas = ref(props.campanhas || []);
const isEditModalOpen = ref(false);
const isEditing = ref(false);
const editCampanhas = ref({
  id: null,
  nome: '',
  comeco: '',
  fim: '',
  publico: '',
  minimo: '',   
  maximo: '', 
  tipo: '',
  video: '',
  capa: '',
  tempo: '',  
  url: '',
  duracao: '',
  regiao: '',
  imagem: []
});

const search = ref('');
const selectedPublico = ref([]);
const selectedTipo = ref([]);

const deleteCampanhas = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta campanha?')) {
    try {
      await router.delete(route('campanhas.destroy', id));

      if (Array.isArray(campanhas.value.data)) {
        campanhas.value.data = campanhas.value.data.filter(campanha => campanha.id !== id);
      }

      window.location.reload(); 
    } catch (error) {
      console.error('Erro ao deletar campanha:', error);
    }
  }
};

const handleCreateItem = () => {
  isEditing.value = false;
  editCampanhas.value = {
    id: null,
    nome: '',
    comeco: '',
    fim: '',
    publico: '',
    minimo: '',   
    maximo: '', 
    tipo: '',
    video: '',
    capa: '',
    tempo: '',  
    url: '',
    duracao: '',
    regiao: '',
    imagem: []
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

const fetchRadios = async () => {
  try {
    const response = await fetch(route('radios.index'), {
      headers: { 'Accept': 'application/json' },
    });

    if (response.ok) {
      const radiosData = await response.json();
      radios.value = radiosData.data;
    } else {
      console.error('Erro ao buscar rádios:', response.statusText);
    }
  } catch (error) {
    console.error('Erro ao buscar rádios:', error);
  }
};

onMounted(() => {
  fetchRadios();
});

const filteredCampanhas = computed(() => {
  const campanhaArray = campanhas.value.data || [];

  return campanhaArray.filter(campanha => {
    const nome = typeof campanha.nome === 'string' ? campanha.nome.toLowerCase() : '';
    const matchesSearch = nome.includes(search.value.toLowerCase());
    const matchesPublico = selectedPublico.value.length > 0 ? selectedPublico.value.includes(campanha.publico) : true;
    const matchesTipo = selectedTipo.value.length > 0 ? selectedTipo.value.includes(campanha.tipo) : true;
    
    return matchesSearch && matchesPublico && matchesTipo;
  });
});

</script>

<template>
  <Head title="Campanhas" />

  <AuthenticatedLayout>
    <v-container class="page-background" fluid fill-height>
      
        <CampanhasForm
          :formData="editCampanhas"
          :radios="radios"
          :fields="{
            nome: { label: 'Nome da campanha', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
            periodo: { label: 'Período da campanha', type: 'date-range', start: 'comeco', startLabel: 'Começo', end: 'fim', endLabel: 'Fim', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            radio: { label: 'Rádios da campanha', rules: [(v) => !!v || 'Rádio é obrigatório'], required: true },
            publico: { label: 'Público', type: 'select', options: ['Homens', 'Mulheres', 'Todos'], required: true },
            idade: { label: 'Idade mínima', type: 'date-range', start: 'minimo', startLabel: 'Mínima', end: 'maxima', endLabel: 'Máxima', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            tipo: { label: 'Tipo de anúncio', type: 'radio', options: [{ text: 'Vídeo', value: 'video' }, { text: 'Imagem', value: 'imagem' }], required: true },
            url: { label: 'Url Destino', rules: [(v) => !!v || 'url é obrigatório'], required: true },
           
          }"
          :isEditing="isEditing"
          title="Campanha Radio"
          createRoute="campanhas.store"
          updateRoute="campanhas.update"
          @cancel="closeEditModal"
        />
     
    </v-container>
  </AuthenticatedLayout>
</template>

<style>
.page-background {
  background-color: #f4f4f9;
  padding: 20px;
}

.add-button {
  border-radius: 8px;
  text-transform: uppercase;
}

.search-field, .filters {
  border-radius: 8px;
  background-color: #ffffff;
}

.card-campanha {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-campanha .v-card-title {
  font-weight: bold;
}

.card-campanha img, video {
  border-radius: 8px;
}
</style>
