<template>
  <Head title="Home" />

  <AuthenticatedLayout>
    <v-container fluid>
      <v-row class="draggable-cards">
        <Draggable
          v-model="cards"
          tag="v-row"
          class="d-flex align-items-stretch"
          :itemKey="getItemKey"
        >
          <template #item="{ element, index, isDragging }">
            <CardComponent :card="element" :isDragging="isDragging" @remove="removeCard" />
          </template>
        </Draggable>
      </v-row>

      <!-- Botão para adicionar cards -->
      <v-btn @click="openCardDialog" color="primary" class="add-card-button" fab dark>
        <v-icon>mdi-plus</v-icon>
      </v-btn>

      <!-- Botão para adicionar notas -->
      <v-btn @click="addNote" color="secondary" class="add-note-button" fab dark>
        <v-icon>mdi-note-plus</v-icon>
      </v-btn>

      <!-- Diálogo para adicionar card -->
      <v-dialog v-model="cardDialog" max-width="500px">
        <CardForm @close="closeCardDialog" @add="addCard" :page="home"/>
      </v-dialog>

      <v-row>
        <Draggable
          v-model="notes"
          tag="v-row"
          class="d-flex align-items-stretch"
          :itemKey="getItemKey"
        >
          <template #item="{ element }">
            <NoteComponent :note="element" @remove="removeNote" />
          </template>
        </Draggable>
      </v-row>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios'; // Importando o axios para fazer a consulta ao backend
import Draggable from 'vuedraggable';
import CardComponent from '@/Components/CardComponent.vue';
import NoteComponent from '@/Components/NoteComponent.vue';
import CardForm from '@/Components/forms/CardForm.vue';

const { props } = usePage();
const cards1 = ref(props.cards || []);
console.log('/*************************************************//**********************');
console.log(cards1);
const totals = reactive({
  users: 0,
  campaigns: 0,
  radios: 0,
});

onMounted(() => {
  totals.users = props.usersCount;
  totals.campaigns = props.campaignsCount;
  totals.radios = props.radiosCount;
});

const cards = ref([
  { id: 1, title: 'Total de Usuários', content: totals.users, icon: 'mdi-account', type: 'Texto' },
  { id: 2, title: 'Total de Campanhas', content: totals.campaigns, icon: 'mdi-bullhorn', type: 'Texto' },
  { id: 3, title: 'Total de Rádios', content: totals.radios, icon: 'mdi-radio-tower', type: 'Texto' },
]);

const notes = ref([]);
const cardDialog = ref(false);
const newCard = ref({
  url: '',
  title: '',
  type: '',
  content: '',
  chartOptions: {},
});

const openCardDialog = () => {
  cardDialog.value = true;
};

const fetchData = async (url) => {
  try {
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    return null;
  }
};

const saveCard = async () => {
  const data = await fetchData(newCard.value.url);

  if (newCard.value.type === 'Texto') {
    newCard.value.content = data;
  } else if (newCard.value.type === 'Gráfico') {
    newCard.value.chartOptions = data;
  }

  cards.value.push({
    id: Date.now(),
    title: newCard.value.title,
    content: newCard.value.content,
    icon: 'mdi-information',
    type: newCard.value.type,
    chartOptions: newCard.value.chartOptions,
  });

  newCard.value = { url: '', title: '', type: '', content: '', chartOptions: {} };
  cardDialog.value = false;
};

const addNote = () => {
  notes.value.push({ id: Date.now(), content: '' });
};

const removeCard = (cardId) => {
  cards.value = cards.value.filter((card) => card.id !== cardId);
};

const removeNote = (noteId) => {
  notes.value = notes.value.filter((note) => note.id !== noteId);
};

const getItemKey = (item) => {
  return item.id; // Utiliza a propriedade 'id' como chave única
};
</script>

<style scoped>
.draggable-cards {
  margin-top: 10px;
  margin-bottom: 20px;
}

.add-card-button {
  position: fixed;
  bottom: 70px;
  right: 16px;
}

.add-note-button {
  position: fixed;
  bottom: 16px;
  right: 16px;
}

@media (max-width: 768px) {
  .add-card-button,
  .add-note-button {
    margin-bottom: 10px;
  }
}
</style>
