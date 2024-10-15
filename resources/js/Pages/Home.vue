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
        <CardForm @close="closeDialog" @add="saveCard" :page="'home'"/>
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
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Draggable from 'vuedraggable';
import CardComponent from '@/Components/CardComponent.vue';
import NoteComponent from '@/Components/NoteComponent.vue';
import CardForm from '@/Components/forms/CardForm.vue';

const { props } = usePage();
const cards = ref(props.cards || []);
const notes = ref([]);
const cardDialog = ref(false);

const fetchCardContent = async (card) => {
  const iconMap = {
    users: 'mdi-account',
    campanhas: 'mdi-bullhorn',
    radio: 'mdi-radio-tower',
    radius: 'mdi-server',
    log: 'mdi-archive',
    default: 'mdi-information',
  };

  try {
    const response = await axios.get(card.url);
    const urlParam = card.url.split('/')[0];
    card.icon = iconMap[urlParam] || iconMap.default;

    if (card.type === 'Texto') {
      card.content = response.data; 
    } else if (card.type === 'Gráfico') {
      //console.log(card)
      card.chartOptions = {
        title: { text: 'Exemplo de Gráfico' },
        tooltip: {},
        xAxis: {
          type: 'category',
          data: ['Jan', 'Feb', 'Mar', 'Apr', 'May']
        },
        yAxis: { type: 'value' },
        series: [
          {
            name: 'Vendas',
            type: 'bar',
            data: [5, 20, 36, 10, 10]
          }
        ]
      };
    }
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    card.content = 'Erro ao carregar';
    card.icon = iconMap.default;
  }
};

const initializeCards = async () => {
  for (const card of cards.value) {
    await fetchCardContent(card);
  }
};

onMounted(() => {
  initializeCards();
});

const openCardDialog = () => {
  cardDialog.value = true;
};
const closeDialog = () => {
  cardDialog.value = false;
};
const saveCard = async () => {
  cardDialog.value = false;
  window.location.reload();
};

const addNote = () => {
  notes.value.push({ id: Date.now(), content: '' });
};

const removeCard = (cardId) => {
  if (confirm('Tem certeza que deseja deletar este Painel?')) {
    try {
      router.delete(route('cards.destroy', cardId));
      cards.value = cards.value.filter((card) => card.id !== cardId);
    } catch (error) {
      console.error('Erro ao deletar Painel:', error);
    }
  }
};

const removeNote = (noteId) => {
  notes.value = notes.value.filter((note) => note.id !== noteId);
};

const getItemKey = (item) => {
  return item.id;
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
