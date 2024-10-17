<template>
  <Head title="Home" />
  <AuthenticatedLayout>
    <v-container fluid>
      
      <!-- Cards Fixos para Total Dispositivos, Novos Clientes e Acessos Hoje -->
      <v-row class="fixed-cards" dense>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Total Dispositivos"
            icon="mdi-radio-tower"
            :value="cardsData.totalRadios"
            color="#42A5F5"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Novos Clientes"
            icon="mdi-account-multiple-plus"
            :value="cardsData.novosUsers"
            color="#66BB6A"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Acessos Hoje"
            icon="mdi-wifi"
            :value="cardsData.totalAcessos"
            color="#FFCA28"
          />
        </v-col>
      </v-row>
      
      <!-- Cards Dinâmicos -->
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
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router,usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Draggable from 'vuedraggable';
import CardComponent from '@/Components/CardComponent.vue';
import FixedCard from '@/Components/FixedCard.vue';
import NoteComponent from '@/Components/NoteComponent.vue';
import CardForm from '@/Components/forms/CardForm.vue';

const { props } = usePage();
const cards = ref(props.cards || []);

const notes = ref([]);
const cardDialog = ref(false);

const cardsData = ref({
  totalRadios: 0,
  novosUsers: 0,
  totalAcessos: 0
});

const fetchFixedCardsData = async () => {
  try {
    const response = await axios.get('radios/getRadiosInfo');
    if (response.data.success && response.data.data) {
      const info = response.data.data;
      cardsData.value.totalRadios = info.totalRadios;
      cardsData.value.novosUsers = info.novosUsers;
      cardsData.value.totalAcessos = info.totalAcessos;
    } else {
      console.error('Estrutura de dados inesperada:', response.data);
    }
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
  }
};

const fetchCardContent = async (card) => {
  const iconMap = {
    users: 'mdi-account',
    campanhas: 'mdi-bullhorn',
    radio: 'mdi-radio-tower',
    radius: 'mdi-server',
    log: 'mdi-archive',
    default: 'mdi-information',
  };
console.log(card)
  try {
    const response = await axios.get(card.url);
    const urlParam = card.url.split('/')[0];
    card.icon = iconMap[urlParam] || iconMap.default;

    if (card.type === 'Texto') {
      card.content = response.data;
    } else if (card.type === 'Gráfico') {
      const data = response.data.data;
      card.chartOptions = {
        title: { text: 'Estatísticas de Dados' },
        tooltip: { trigger: 'axis' },
        xAxis: {
          type: 'category',
          data: data.labels // Verifique se o backend está retornando um array `labels` para o eixo X
        },
        yAxis: { type: 'value' },
        series: data.dados.map((serie) => ({
          name: serie.label,
          type: 'line', // Ajuste o tipo de gráfico aqui, se necessário
          data: serie.data
        }))
      };
    }
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    card.content = 'Erro ao carregar';
    card.icon = iconMap.default;
  }
};

const initializeCards = async () => {
  console.log('Inicializando cards...',cards.value);
  for (const card of cards.value) {
    await fetchCardContent(card);
  }
};

onMounted(() => {
  fetchFixedCardsData();
  initializeCards();
});

const openCardDialog = () => {
  cardDialog.value = true;
};
const closeDialog = () => {
  cardDialog.value = false;
};





</script>

<style scoped>
.fixed-cards {
  margin-top: 10px;
}

.draggable-cards {
  margin-top: 20px;
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
</style>
