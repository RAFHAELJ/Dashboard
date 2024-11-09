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


const reloadData = async () => {
  try {
    // Faz a chamada para buscar novamente as campanhas
    const response = await fetch(route('campanhas.index',[], true), {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      campanhas.value = data; // Atualiza a lista de campanhas
    } else {
      console.error('Erro ao recarregar campanhas:', response.statusText);
    }
  } catch (error) {
    console.error('Erro ao recarregar campanhas:', error);
  }
};


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
    const response = await fetch(route('radios.index',[], true), {
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
    <template v-slot="{ canAccess }">
    <v-container class="page-background" fluid fill-height>
      <!-- Filtros e Botão de Adicionar na Mesma Linha -->
      <v-row class="align-center">
        <v-col cols="12" md="4" class="py-1">
          <v-text-field
            v-model="search"
            label="Buscar Campanha"
            prepend-icon="mdi-magnify"
            density="compact"
            class="search-field"
          />
        </v-col>
        <v-col cols="12" md="3" class="py-1">
          <v-select
            v-model="selectedPublico"
            :items="['Homens', 'Mulheres', 'Todos']"
            label="Filtrar por Público"
            density="compact"
            multiple
            chips
            class="filters"
          />
        </v-col>
        <v-col cols="12" md="3" class="py-1">
          <v-select
            v-model="selectedTipo"
            :items="['Imagem', 'Vídeo']"
            label="Filtrar por Tipo"
            density="compact"
            multiple
            chips
            class="filters"
          />
        </v-col>
        <v-col cols="12" md="2" class="py-1 d-flex justify-end align-center">
          <v-btn
            v-if="canAccess('campanhas','gravar')"
            @click="handleCreateItem"
            density="comfortable"
            color="primary"
            class="add-button"
            block
          >
            <v-icon start>mdi-plus</v-icon>
            Adicionar Campanha
          </v-btn>
        </v-col>
      </v-row>

      <!-- Listagem de Campanhas em Formato de Card -->
      <v-row>
        <v-col cols="12" sm="6" md="4" lg="3" v-for="campanha in filteredCampanhas" :key="campanha.id">
          <v-card class="card-campanha" hover v-if="canAccess('campanhas','ler')">
            <v-card-title>{{ campanha.nome }}</v-card-title>
            <v-card-text>
              <p><strong>Começo:</strong> {{ campanha.comeco }}</p>
              <p><strong>Fim:</strong> {{ campanha.fim }}</p>
              <p><strong>Público:</strong> {{ campanha.publico }}</p>
              <p><strong>Tipo:</strong> {{ campanha.tipo }}</p>
              <p><strong>Duração:</strong> {{ campanha.duracao }} segundos</p>
              <v-img
                v-if="campanha.imagem"
                :src="`/storage/${campanha.imagem}`"
                alt="Imagem da campanha"
                max-height="150"
              ></v-img>
              <v-img
                v-if="campanha.capa"
                :src="`/storage/${campanha.capa}`"
                alt="Imagem da campanha"
                max-height="150"
              ></v-img>
              <video
                v-if="campanha.video"
                controls
                :src="`/storage/${campanha.video}`"
                width="320"
                height="240"
              >
                Seu navegador não suporta vídeos.
              </video>
            </v-card-text>

            <v-card-actions>
              <v-btn 
              v-if="canAccess('campanhas','atualizar')"
              icon @click="handleEditItem(campanha)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn 
              v-if="canAccess('campanhas','excluir')"
              icon @click="handleDeleteItem(campanha)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <!-- Formulário para Edição/Criar Campanhas -->
      <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <CampanhasForm
          :formData="editCampanhas"
          :radios="radios"
          :fields="{
            nome: { label: 'Nome da campanha', rules: [(v) => !!v || 'Nome é obrigatório'], required: true },
            periodo: { label: 'Período da campanha', type: 'date-range', start: 'comeco', startLabel: 'Começo', end: 'fim', endLabel: 'Fim', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            radio: { label: 'Rádios da campanha', rules: [(v) => !!v || 'Rádio é obrigatório'], required: true },
            publico: { label: 'Público', type: 'select', options: ['Homens', 'Mulheres', 'Todos'], required: true },
            idade: { label: 'Idade mínima', type: 'date-range', start: 'minimo', startLabel: 'Mínima', end: 'maxima', endLabel: 'Máxima', rules: [(v) => !!v || 'Campo obrigatório'], required: true },
            tipo: { label: 'Tipo de anúncio', type: 'radio', options: [{ text: 'Vídeo', value: 'video' }, { text: 'Imagem', value: 'imagem' }, { text: 'Formulario', value: 'formulario' }], required: true },
            url: { label: 'Url Destino', rules: [(v) => !!v || 'url é obrigatório'], required: true },
            tempo: { label: 'Duração', type: 'slider', min: 1, max: 60, required: true }
          }"
          :isEditing="isEditing"
          @formSubmitted="reloadData"
          title="Campanha Radio"
          createRoute="campanhas.store"
          updateRoute="campanhas.update"
          @cancel="closeEditModal"
        />
      </v-dialog>
    </v-container>
    </template>
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
